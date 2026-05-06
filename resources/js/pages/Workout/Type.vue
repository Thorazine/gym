<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';

interface User {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
}>();

const workoutTypes = [
    { id: 'video', label: 'Start Workout Video' },
    { id: 'full', label: 'Start Full Body' },
    { id: 'upper', label: 'Start Upper Body' },
    { id: 'lower', label: 'Start Lower Body' },
    { id: 'butt', label: 'Start Butt' },
    { id: 'core', label: 'Start Core' },
];
</script>

<template>
    <GymLayout :title="`Workout Type - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-10">
                Choose Workout
            </h1>
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 pb-12 content-center max-w-4xl mx-auto w-full">
                <Link 
                    v-for="type in workoutTypes" 
                    :key="type.id"
                    :href="type.id === 'video' ? `/users/${user.id}/workout/video-categories` : `/users/${user.id}/workout/count?type=${type.id}`"
                    class="h-20 md:h-32 flex items-center justify-center bg-gray-900 border-4 border-gray-800 rounded-3xl hover:bg-white hover:text-black hover:border-white transition-all group"
                >
                    <span class="text-xl sm:text-2xl md:text-4xl font-black uppercase tracking-widest px-6 text-center line-clamp-2 leading-tight">
                        {{ type.label }}
                    </span>
                </Link>
            </div>
        </div>

    </GymLayout>
</template>
