<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

interface User {
    id: number;
    name: string;
}

interface Video {
    id: number;
    title: string;
    video_id: string;
}

const props = defineProps<{
    user: User;
    video: Video;
}>();

const isPlaying = ref(true);
const isComplete = ref(false);
const workoutId = ref<number | null>(null);
const currentTime = ref(0);
const duration = ref(1); // default to 1 to avoid div by zero initially
let player: any = null;
let progressInterval: any = null;

const progressPercentage = computed(() => {
    return Math.min(100, Math.max(0, (currentTime.value / duration.value) * 100));
});

function formatTime(seconds: number) {
    if (isNaN(seconds) || seconds < 0) return '0:00';
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return `${m}:${s < 10 ? '0' : ''}${s}`;
}

function startProgressTracking() {
    stopProgressTracking();
    progressInterval = setInterval(() => {
        if (player && typeof player.getCurrentTime === 'function') {
            currentTime.value = player.getCurrentTime();
        }
        if (player && typeof player.getDuration === 'function') {
            const d = player.getDuration();
            if (d > 0) duration.value = d;
        }
    }, 1000);
}

function stopProgressTracking() {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
}

function seekToPosition(event: MouseEvent) {
    if (!player || typeof player.seekTo !== 'function') return;
    const target = event.currentTarget as HTMLElement;
    const rect = target.getBoundingClientRect();
    const clickX = event.clientX - rect.left;
    const newPercentage = Math.max(0, Math.min(1, clickX / rect.width));
    const newTime = newPercentage * duration.value;
    player.seekTo(newTime, true);
    currentTime.value = newTime;
}

const initPlayer = () => {
    player = new (window as any).YT.Player('youtube-player', {
        height: '100%',
        width: '100%',
        videoId: props.video.video_id,
        playerVars: {
            autoplay: 1,
            controls: 0,
            modestbranding: 1,
            rel: 0,
            showinfo: 0,
        },
        events: {
            onReady: onPlayerReady,
            onStateChange: onPlayerStateChange
        }
    });
};

onMounted(async () => {
    if ((window as any).YT && (window as any).YT.Player) {
        initPlayer();
    } else {
        (window as any).onYouTubeIframeAPIReady = initPlayer;

        if (!document.getElementById('youtube-iframe-api')) {
            const tag = document.createElement('script');
            tag.id = 'youtube-iframe-api';
            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            if (firstScriptTag && firstScriptTag.parentNode) {
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            } else {
                document.head.appendChild(tag);
            }
        }
    }

    try {
        const createResponse = await axios.post(`/users/${props.user.id}/workout`, {
            type: 'Video Workout',
            start_time: new Date().toISOString(),
        });
        workoutId.value = createResponse.data.id;
    } catch (e) {
        console.error('Failed to create workout record', e);
    }
});

onUnmounted(() => {
    stopProgressTracking();
    if (player && typeof player.destroy === 'function') {
        player.destroy();
    }
});

function onPlayerReady(event: any) {
    event.target.playVideo();
}

function onPlayerStateChange(event: any) {
    // 1 is PLAYING, 2 is PAUSED, 0 is ENDED
    if (event.data === 1) {
        isPlaying.value = true;
        isComplete.value = false;
        startProgressTracking();
    } else if (event.data === 2) {
        isPlaying.value = false;
        stopProgressTracking();
    } else if (event.data === 0) {
        isPlaying.value = false;
        isComplete.value = true;
        stopProgressTracking();
    }
}

function togglePlayPause() {
    if (player) {
        if (isPlaying.value) {
            player.pauseVideo();
        } else {
            player.playVideo();
        }
    }
}

function skipBackward() {
    if (player && typeof player.getCurrentTime === 'function' && typeof player.seekTo === 'function') {
        const newTime = Math.max(0, player.getCurrentTime() - 10);
        player.seekTo(newTime, true);
        currentTime.value = newTime;
    }
}

function skipForward() {
    if (player && typeof player.getCurrentTime === 'function' && typeof player.seekTo === 'function') {
        const newTime = player.getCurrentTime() + 30;
        player.seekTo(newTime, true);
        currentTime.value = Math.min(newTime, duration.value);
    }
}

function playAgain() {
    isComplete.value = false;
    if (player && typeof player.seekTo === 'function') {
        player.seekTo(0, true);
        player.playVideo();
    }
}

async function finishWorkout() {
    if (workoutId.value) {
        try {
            await axios.put(`/workouts/${workoutId.value}`, {
                end_time: new Date().toISOString(),
            });
        } catch (e) {
            console.error('Failed to update workout end time', e);
        }
    }
    router.get('/last-workout');
}

async function stopWorkout() {
    if (workoutId.value) {
        try {
            await axios.put(`/workouts/${workoutId.value}`, {
                end_time: new Date().toISOString(),
            });
        } catch (e) {
            console.error('Failed to update workout end time', e);
        }
    }
    router.get(`/users/${props.user.id}/type`);
}

</script>

<template>
    <div class="flex-1 min-h-0 w-full relative">
        <div class="absolute inset-0 bg-black flex flex-col overflow-hidden">
            <Head :title="`Playing: ${video.title}`" />
            
            <!-- Video & Controls (visible when playing) -->
            <div v-show="!isComplete" class="flex-1 w-full flex flex-col overflow-hidden">
                <!-- Video Container -->
                <div class="flex-1 min-h-0 w-full flex items-center justify-center">
                    <div class="aspect-video w-full max-h-full bg-gray-900">
                        <div id="youtube-player" class="w-full h-full pointer-events-none"></div>
                    </div>
                </div>

                <!-- Controls Area -->
                <div class="w-full shrink-0 flex flex-col bg-black pb-2">
                    
                    <!-- Progress Bar -->
                    <div class="w-full px-0">
                        <div class="w-full h-2 sm:h-3 bg-gray-800 overflow-hidden relative cursor-pointer" @click="seekToPosition">
                            <div class="absolute top-0 left-0 h-full bg-red-600 transition-all duration-300 ease-linear" :style="{ width: `${progressPercentage}%` }"></div>
                        </div>
                        <div class="flex justify-between text-[10px] sm:text-xs text-gray-400 font-mono mt-1 px-2">
                            <span>{{ formatTime(currentTime) }}</span>
                            <span>{{ formatTime(duration) }}</span>
                        </div>
                    </div>

                    <div class="w-full flex justify-center items-center gap-4 sm:gap-8 px-4 pt-1 pb-2">
                        
                        <button 
                            @click="skipBackward"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 text-white flex flex-col items-center justify-center hover:bg-white/30 hover:scale-105 active:scale-95 transition-all shadow-xl backdrop-blur-sm"
                            title="Skip backward 10 seconds"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                            </svg>
                            <span class="text-[10px] sm:text-xs font-semibold mt-0.5">-10s</span>
                        </button>

                        <button 
                            @click="togglePlayPause"
                            class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white text-black flex items-center justify-center hover:bg-gray-200 hover:scale-105 active:scale-95 transition-all shadow-xl mx-2"
                        >
                            <!-- Pause Icon -->
                            <svg v-if="isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Play Icon -->
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12 ml-1 sm:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        <button 
                            @click="skipForward"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 text-white flex flex-col items-center justify-center hover:bg-white/30 hover:scale-105 active:scale-95 transition-all shadow-xl backdrop-blur-sm"
                            title="Skip forward 30 seconds"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.334-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z" />
                            </svg>
                            <span class="text-[10px] sm:text-xs font-semibold mt-0.5">+30s</span>
                        </button>

                        <button 
                            @click="stopWorkout"
                            class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 hover:scale-105 active:scale-95 transition-all shadow-xl ml-2 sm:ml-4"
                        >
                            <!-- Stop Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                            </svg>
                        </button>

                    </div>
                </div>
            </div>

            <!-- Completion Screen -->
            <div v-if="isComplete" class="flex-1 flex flex-col justify-center items-center w-full px-4 py-8 relative z-10 bg-black/90 backdrop-blur-md">
                <h1 class="text-5xl sm:text-7xl md:text-8xl font-black uppercase tracking-widest mb-12 md:mb-16 text-center text-white">
                    WORKOUT<br>COMPLETE!
                </h1>
                
                <div class="flex flex-col sm:flex-row gap-6 w-full max-w-2xl px-4">
                    <button 
                        @click="playAgain"
                        class="flex-1 py-6 md:py-8 bg-white/10 hover:bg-white/20 text-white rounded-[3rem] flex flex-col items-center justify-center gap-3 transition-transform active:scale-95 border-2 border-white/20 backdrop-blur-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 md:w-16 md:h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="text-2xl md:text-3xl font-black uppercase tracking-widest">AGAIN</span>
                    </button>
                    
                    <button 
                        @click="finishWorkout"
                        class="flex-1 py-6 md:py-8 bg-red-600 hover:bg-red-700 text-white rounded-[3rem] flex flex-col items-center justify-center gap-3 transition-transform active:scale-95 shadow-2xl shadow-red-600/30"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 md:w-16 md:h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-2xl md:text-3xl font-black uppercase tracking-widest text-center">END WORKOUT</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Prevent text selection */
* {
    user-select: none;
}
</style>
