<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';

interface User {
    id: number;
    name: string;
}

interface VideoCategory {
    id: number;
    name: string;
}

interface Video {
    id: number;
    title: string;
    placeholder_url: string;
}

const props = defineProps<{
    user: User;
    category: VideoCategory;
    videos: Video[];
}>();

</script>

<template>
    <GymLayout :title="`${category.name} Videos - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-10">
                {{ category.name }}
            </h1>
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-12 content-start">
                <Link 
                    v-for="video in videos" 
                    :key="video.id"
                    :href="`/users/${user.id}/workout/videos/${video.id}/play`"
                    class="flex flex-col bg-gray-900 border-4 border-gray-800 rounded-3xl overflow-hidden hover:bg-gray-800 hover:border-white transition-all group cursor-pointer"
                >
                    <div class="aspect-video w-full bg-black relative">
                        <img :src="video.placeholder_url" :alt="video.title" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" />
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-white group-hover:text-red-500 transition-colors line-clamp-2">
                            {{ video.title }}
                        </h2>
                    </div>
                </Link>
            </div>
            
            <div v-if="videos.length === 0" class="text-center text-gray-400 text-2xl mt-10">
                No videos available in this category. Please add some via the terminal command.
            </div>
        </div>

    </GymLayout>
</template>
