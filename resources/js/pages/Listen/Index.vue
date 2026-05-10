<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';

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
    soundclouds: Soundcloud[];
}>();
</script>

<template>
    <GymLayout :title="`Listen to Music - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-10">
                Choose Music
            </h1>
            
            <div class="flex-1 flex flex-col gap-6 pb-12 overflow-y-auto">
                <div v-if="soundclouds.length === 0" class="text-center text-gray-400 text-2xl font-bold uppercase mt-12">
                    No music available. Go to settings to add some!
                </div>

                <Link 
                    v-for="music in soundclouds" 
                    :key="music.id"
                    :href="`/users/${user.id}/listen/${music.id}`"
                    class="h-24 md:h-32 flex items-center justify-center bg-gray-900 border-4 border-gray-800 rounded-3xl hover:bg-white hover:text-black hover:border-white transition-all group shrink-0"
                >
                    <span class="text-2xl sm:text-3xl md:text-5xl font-black uppercase tracking-widest px-6 text-center line-clamp-2 leading-tight">
                        {{ music.title }}
                    </span>
                </Link>
            </div>
        </div>

    </GymLayout>
</template>
