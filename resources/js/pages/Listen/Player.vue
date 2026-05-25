<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import { ChevronLeft, Play, Pause } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
}

interface Soundcloud {
    id: number;
    title: string;
    url: string;
}

const props = defineProps<{
    user: User;
    soundcloud: Soundcloud;
}>();

const isMusicReady = ref(false);
const isPaused = ref(true);
let scWidget: any = null;

onMounted(() => {
    const script = document.createElement('script');
    script.src = 'https://w.soundcloud.com/player/api.js';
    script.onload = () => {
        const iframe = document.getElementById('sc-widget') as HTMLIFrameElement;
        if (iframe && (window as any).SC) {
            scWidget = (window as any).SC.Widget(iframe);
            scWidget.bind((window as any).SC.Widget.Events.READY, () => {
                isMusicReady.value = true;
                
                scWidget.bind((window as any).SC.Widget.Events.PLAY, () => {
                    isPaused.value = false;
                });

                scWidget.bind((window as any).SC.Widget.Events.PAUSE, () => {
                    isPaused.value = true;
                });
            });
            
            // Fallback in case READY doesn't fire
            setTimeout(() => {
                isMusicReady.value = true;
            }, 3000);
        }
    };
    document.body.appendChild(script);
});

const togglePause = () => {
    if (scWidget) {
        if (isPaused.value) {
            scWidget.play();
        } else {
            scWidget.pause();
        }
    }
};
</script>

<template>
    <GymLayout :title="`Now Playing: ${soundcloud.title}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4 max-w-4xl mx-auto w-full gap-8">
            <div class="text-center">
                <h2 class="text-2xl text-gray-400 font-bold uppercase tracking-widest mb-2">
                    Now Playing
                </h2>
                <h1 class="text-4xl md:text-6xl font-black uppercase tracking-widest text-white mb-6 line-clamp-2">
                    {{ soundcloud.title }}
                </h1>
            </div>

            <!-- Music Player -->
            <div class="w-full bg-gray-900 border-4 border-gray-800 rounded-3xl p-6 md:p-12 shadow-2xl flex flex-col items-center">
                
                <iframe 
                    id="sc-widget" 
                    :src="'https://w.soundcloud.com/player/?url=' + encodeURIComponent(soundcloud.url) + '&auto_play=false&hide_related=true&show_comments=false&show_user=false&show_reposts=false&visual=false'" 
                    width="100%" 
                    height="166" 
                    scrolling="no" 
                    frameborder="no" 
                    allow="autoplay" 
                    class="rounded-2xl shadow-xl border-2 border-black bg-black w-full mb-8"
                ></iframe>

                <div v-if="!isMusicReady" class="text-white text-xl uppercase tracking-widest font-bold">
                    Loading Player...
                </div>
                <div v-else class="flex justify-center">
                    <button 
                        @click="togglePause"
                        class="w-24 h-24 bg-white text-black rounded-full flex items-center justify-center hover:bg-gray-200 transition-transform active:scale-95 shadow-xl"
                    >
                        <Play v-if="isPaused" class="w-12 h-12 fill-black translate-x-1" />
                        <Pause v-else class="w-12 h-12 fill-black" />
                    </button>
                </div>

            </div>

            <!-- Back Button -->
            <div class="flex justify-center mt-4">
                <Link 
                    :href="`/users/${user.id}/listen`"
                    class="py-6 px-12 border-4 border-gray-600 text-gray-400 rounded-3xl flex items-center justify-center gap-4 hover:border-white hover:text-white transition-all group active:scale-95"
                >
                    <ChevronLeft class="w-10 h-10" />
                    <span class="text-2xl font-black uppercase tracking-widest">Back to Music</span>
                </Link>
            </div>
        </div>

    </GymLayout>
</template>
