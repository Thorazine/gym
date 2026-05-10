<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import { ChevronLeft } from 'lucide-vue-next';

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
            <div class="w-full bg-gray-900 border-4 border-gray-800 rounded-3xl p-6 md:p-12 shadow-2xl">
                <iframe 
                    id="sc-widget" 
                    :src="'https://w.soundcloud.com/player/?url=' + encodeURIComponent(soundcloud.url) + '&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&visual=true'" 
                    width="100%" 
                    height="450" 
                    scrolling="no" 
                    frameborder="no" 
                    allow="autoplay" 
                    class="rounded-2xl shadow-xl border-2 border-black bg-black w-full"
                ></iframe>
                <div class="mt-8 text-center text-gray-400 text-sm font-bold uppercase">
                    Press play above to start listening.
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
