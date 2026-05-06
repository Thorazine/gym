<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import { Play, Pause, SkipForward, RotateCcw, XCircle } from 'lucide-vue-next';
import axios from 'axios';

interface Exercise {
    id: number;
    title: string;
}

interface User {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
    workoutType: string;
    exerciseCount: string | number;
    timing: string | number; // '1', '2', or '3'
    exercises: Exercise[];
    exerciseIds: string;
    musicUrl?: string | null;
}>();

// Map timing option to work/rest/rounds
const timingsMap = ref<Record<string, { work: number; rest: number; rounds: number }>>({});
const timingSettings = ref({ work: 30, rest: 10, rounds: 3 }); // Fallback defaults
const isLoaded = ref(false);
const isMusicReady = ref(!props.musicUrl);
const hasStarted = ref(false);

// State
const phase = ref<'prep' | 'work' | 'rest' | 'done'>('prep');
const timeLeft = ref(5); // 5 seconds prep before starting
const currentExerciseIndex = ref(0);
const currentRound = ref(1);
const isPaused = ref(false);
const workoutId = ref<number | null>(null);

let timerInterval: ReturnType<typeof setInterval> | null = null;
let beepAudio: HTMLAudioElement | null = null;
let scWidget: any = null;

const currentExercise = computed(() => {
    if (props.exercises.length === 0) return null;
    return props.exercises[currentExerciseIndex.value];
});

onMounted(async () => {
    try {
        const response = await axios.get('/api/gym/config');
        const timings = response.data.timings;
        const map: Record<string, any> = {};
        for (const t of timings) {
            map[t.id.toString()] = { work: t.work, rest: t.rest, rounds: t.rounds };
        }
        timingsMap.value = map;
        timingSettings.value = map[props.timing.toString()] || map['1'] || { work: 30, rest: 10, rounds: 3 };
        isLoaded.value = true;
    } catch (e) {
        console.error('Failed to load timings API', e);
        // Defaults if it fails
        isLoaded.value = true;
    }

    // Attempt to preload audio
    try {
        beepAudio = new Audio('/sounds/beeps/beep.mp3');
        beepAudio.load();
    } catch (e) {
        console.warn('Audio could not be loaded.');
    }
    
    if (props.musicUrl) {
        const script = document.createElement('script');
        script.src = 'https://w.soundcloud.com/player/api.js';
        script.onload = () => {
            const iframe = document.getElementById('sc-widget') as HTMLIFrameElement;
            if (iframe && (window as any).SC) {
                scWidget = (window as any).SC.Widget(iframe);
                scWidget.bind((window as any).SC.Widget.Events.READY, () => {
                    isMusicReady.value = true;
                    
                    scWidget.bind((window as any).SC.Widget.Events.PLAY, () => {
                        if (!hasStarted.value) {
                            initWorkout(false); // Start workout without trying to play widget again
                        } else if (isPaused.value) {
                            isPaused.value = false;
                        }
                    });

                    scWidget.bind((window as any).SC.Widget.Events.PAUSE, () => {
                        if (hasStarted.value && !isPaused.value) {
                            isPaused.value = true;
                        }
                    });
                });
                
                // Fallback in case READY doesn't fire
                setTimeout(() => {
                    isMusicReady.value = true;
                }, 3000);
            }
        };
        document.body.appendChild(script);
    }
});

const initWorkout = async (playWidget = true) => {
    hasStarted.value = true;
    
    // Attempt to unlock audio context
    if (beepAudio) {
        beepAudio.play().catch(() => {});
        beepAudio.pause();
        beepAudio.currentTime = 0;
    }
    
    if (scWidget && playWidget) {
        scWidget.play();
    }
    
    try {
        const createResponse = await axios.post(`/users/${props.user.id}/workout`, {
            type: props.workoutType,
            start_time: new Date().toISOString(),
        });
        workoutId.value = createResponse.data.id;
    } catch (e) {
        console.error('Failed to create workout', e);
    }
    
    startTimer();
};

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

const playBeep = () => {
    if (beepAudio) {
        // Reset to start just in case it's currently playing
        beepAudio.currentTime = 0;
        beepAudio.play().catch(e => console.warn('Audio play prevented by browser policy'));
    }
};

// Play beep on 3, 2, 1
watch(timeLeft, (newVal) => {
    if (newVal === 3 || newVal === 2 || newVal === 1) {
        playBeep();
    }
});

const startTimer = () => {
    if (timerInterval) clearInterval(timerInterval);
    
    timerInterval = setInterval(() => {
        if (isPaused.value) return;
        
        if (timeLeft.value > 0) {
            timeLeft.value--;
            
            // Long beep on exactly 0? Let's just play another beep
            if (timeLeft.value === 0) {
                playBeep();
            }
        } else {
            transitionPhase();
        }
    }, 1000);
};

const transitionPhase = () => {
    if (phase.value === 'prep') {
        phase.value = 'work';
        timeLeft.value = timingSettings.value.work;
    } 
    else if (phase.value === 'work') {
        // If we just finished work, either we go to rest, or to the next exercise/done
        if (currentRound.value < timingSettings.value.rounds) {
            phase.value = 'rest';
            timeLeft.value = timingSettings.value.rest;
        } else {
            // Finished all rounds for this exercise
            nextExercise();
        }
    }
    else if (phase.value === 'rest') {
        // Rest is over, go back to work for next round
        currentRound.value++;
        phase.value = 'work';
        timeLeft.value = timingSettings.value.work;
    }
};

const nextExercise = () => {
    if (currentExerciseIndex.value < props.exercises.length - 1) {
        currentExerciseIndex.value++;
        currentRound.value = 1;
        phase.value = 'prep';
        timeLeft.value = 5; // Prep again for the next exercise
    } else {
        phase.value = 'done';
        if (timerInterval) clearInterval(timerInterval);
    }
};

const togglePause = () => {
    isPaused.value = !isPaused.value;
    
    if (scWidget) {
        if (isPaused.value) {
            scWidget.pause();
        } else {
            scWidget.play();
        }
    }
};

const skipExercise = () => {
    nextExercise();
};

const endWorkout = async () => {
    if (workoutId.value) {
        try {
            await axios.put(`/workouts/${workoutId.value}`, {
                end_time: new Date().toISOString(),
            });
        } catch (e) {
            console.error('Failed to update workout end time', e);
        }
    }
    router.visit('/dashboard');
};

const phaseText = computed(() => {
    if (phase.value === 'prep') return 'GET READY';
    if (phase.value === 'work') return 'WORK';
    if (phase.value === 'rest') return 'REST';
    return 'DONE';
});

const formatTime = (seconds: number) => {
    // Just a large number, but we can format it if it goes over 60s
    if (seconds >= 60) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${m}:${s.toString().padStart(2, '0')}`;
    }
    return seconds.toString();
};

</script>

<template>
    <GymLayout :title="`Workout Timer - ${user.name}`" :show-back-button="false">
        
        <div v-if="!isLoaded || !isMusicReady" class="flex flex-col h-full justify-center items-center text-white">
            <p class="text-3xl font-bold uppercase tracking-wider">Loading...</p>
        </div>
        <div v-else class="flex flex-col h-full justify-center items-center select-none w-full max-w-2xl mx-auto px-4">
            
            <div class="w-full mb-8 transition-opacity" :class="{'opacity-50 pointer-events-none': phase === 'done'}" v-show="musicUrl">
                <iframe 
                    v-if="musicUrl"
                    id="sc-widget" 
                    :src="'https://w.soundcloud.com/player/?url=' + encodeURIComponent(musicUrl) + '&auto_play=false&hide_related=true&show_comments=false&show_user=false&show_reposts=false&visual=false'" 
                    width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay" class="rounded-2xl shadow-xl shadow-black/50 border-4 border-gray-800 bg-black"
                ></iframe>
            </div>

            <!-- Start Screen -->
            <template v-if="!hasStarted">
                <button 
                    @click="initWorkout(true)"
                    class="py-12 px-24 w-full bg-white text-black rounded-[3rem] flex flex-col items-center justify-center gap-4 hover:bg-gray-200 transition-transform active:scale-95 shadow-2xl shadow-black/50"
                >
                    <Play class="w-24 h-24 md:w-32 md:h-32 fill-black translate-x-2" />
                    <span class="text-4xl md:text-6xl font-black uppercase tracking-widest text-center leading-tight">START</span>
                    <span v-if="musicUrl" class="text-lg text-gray-500 font-bold uppercase tracking-wider text-center mt-2 px-4">(iOS: Tap play on the music widget above)</span>
                </button>
            </template>
            
            <template v-else-if="phase !== 'done'">
                <!-- Exercise Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl md:text-5xl font-bold uppercase tracking-wider text-gray-400 mb-2">
                        {{ phaseText }}
                    </h2>
                    <h1 class="text-5xl md:text-7xl font-black uppercase tracking-widest text-white">
                        {{ currentExercise?.title || 'Unknown' }}
                    </h1>
                    <div class="text-2xl text-gray-500 font-bold uppercase mt-4">
                        Exercise {{ currentExerciseIndex + 1 }} / {{ exercises.length }} 
                        <span class="mx-4">|</span> 
                        Round {{ currentRound }} / {{ timingSettings.rounds }}
                    </div>
                </div>
                
                <!-- Giant Countdown -->
                <div 
                    class="text-[12rem] md:text-[20rem] font-black leading-none mb-12 tabular-nums px-12 md:px-20"
                    :class="{
                        'text-yellow-500': phase === 'prep',
                        'text-white': phase === 'work',
                        'text-blue-500': phase === 'rest',
                        'opacity-50': isPaused
                    }"
                >
                    {{ formatTime(timeLeft) }}
                </div>
                
                <!-- Controls -->
                <div class="flex items-center gap-8">
                    <button 
                        @click="togglePause"
                        class="w-24 h-24 md:w-32 md:h-32 bg-white text-black rounded-full flex items-center justify-center hover:bg-gray-200 transition-transform active:scale-95"
                    >
                        <Play v-if="isPaused" class="w-12 h-12 md:w-16 md:h-16 fill-black translate-x-1" />
                        <Pause v-else class="w-12 h-12 md:w-16 md:h-16 fill-black" />
                    </button>
                    
                    <button 
                        @click="skipExercise"
                        class="w-20 h-20 md:w-24 md:h-24 border-4 border-gray-600 text-gray-400 rounded-full flex items-center justify-center hover:border-white hover:text-white transition-colors active:scale-95"
                        title="Skip Exercise"
                    >
                        <SkipForward class="w-8 h-8 md:w-10 md:h-10" />
                    </button>
                </div>
            </template>
            
            <!-- Completion Screen -->
            <template v-else>
                <h1 class="text-7xl md:text-9xl font-black uppercase tracking-widest mb-16 text-center">
                    WORKOUT<br>COMPLETE!
                </h1>
                
                <div class="flex flex-col md:flex-row gap-8 w-full max-w-4xl px-4">
                    <!-- Go Again uses exact same URL -->
                    <Link 
                        :href="`/users/${user.id}/workout/timer?type=${workoutType}&count=${exerciseCount}&timing=${timing}&exercises=${exerciseIds}`" 
                        class="flex-1 py-10 bg-white text-black rounded-3xl flex flex-col items-center justify-center gap-4 hover:bg-gray-200 transition-transform hover:scale-105"
                    >
                        <RotateCcw class="w-16 h-16" />
                        <span class="text-4xl font-black uppercase tracking-widest">GO AGAIN?</span>
                    </Link>
                    
                    <!-- End Workout goes to dashboard -->
                    <button 
                        @click="endWorkout"
                        class="flex-1 py-10 border-4 border-gray-600 text-gray-400 rounded-3xl flex flex-col items-center justify-center gap-4 hover:border-white hover:text-white transition-transform hover:scale-105"
                    >
                        <XCircle class="w-16 h-16" />
                        <span class="text-4xl font-black uppercase tracking-widest">END WORKOUT</span>
                    </button>
                </div>
            </template>
            
        </div>
    </GymLayout>
</template>
