<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

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
let player: any = null;

onMounted(() => {
    // Load YouTube IFrame API script
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    const firstScriptTag = document.getElementsByTagName('script')[0];
    if (firstScriptTag && firstScriptTag.parentNode) {
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    // Define the global callback
    (window as any).onYouTubeIframeAPIReady = () => {
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
});

onUnmounted(() => {
    if (player && typeof player.destroy === 'function') {
        player.destroy();
    }
    // Cleanup global scope
    delete (window as any).onYouTubeIframeAPIReady;
});

function onPlayerReady(event: any) {
    event.target.playVideo();
}

function onPlayerStateChange(event: any) {
    // 1 is PLAYING, 2 is PAUSED
    if (event.data === 1) {
        isPlaying.value = true;
    } else if (event.data === 2) {
        isPlaying.value = false;
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

function stopWorkout() {
    router.get(`/users/${props.user.id}/type`);
}

</script>

<template>
    <div class="h-screen w-screen bg-black flex flex-col overflow-hidden relative">
        <Head :title="`Playing: ${video.title}`" />
        
        <!-- Video Container -->
        <div class="flex-1 w-full h-full relative">
            <div id="youtube-player" class="absolute inset-0 w-full h-full pointer-events-none"></div>
        </div>

        <!-- Controls Overlay -->
        <div class="absolute bottom-0 left-0 right-0 p-8 flex justify-center items-center gap-8 bg-gradient-to-t from-black/80 to-transparent">
            
            <button 
                @click="togglePlayPause"
                class="w-24 h-24 rounded-full bg-white text-black flex items-center justify-center hover:bg-gray-200 hover:scale-105 active:scale-95 transition-all shadow-xl"
            >
                <!-- Pause Icon -->
                <svg v-if="isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Play Icon -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>

            <button 
                @click="stopWorkout"
                class="w-24 h-24 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 hover:scale-105 active:scale-95 transition-all shadow-xl"
            >
                <!-- Stop Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                </svg>
            </button>

        </div>
    </div>
</template>

<style scoped>
/* Prevent text selection */
* {
    user-select: none;
}
</style>
