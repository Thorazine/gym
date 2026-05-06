<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import { Play } from 'lucide-vue-next';

interface Exercise {
    id: number;
    title: string;
    // We don't have images in the DB yet, but we'll leave room for it
    image_url?: string; 
}

interface User {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
    workoutType: string;
    exerciseCount: string | number;
    timing: string | number;
    exercises: Exercise[];
    exerciseIds: string;
}>();

</script>

<template>
    <GymLayout :title="`Workout Setup - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <div>
                    <h1 class="text-5xl md:text-6xl font-black uppercase tracking-widest mb-2">
                        Workout Setup
                    </h1>
                    <p class="text-gray-400 text-2xl uppercase tracking-wider">
                        Review your {{ exercises.length }} exercises
                    </p>
                </div>

                <!-- Ready Button -->
                <Link 
                    :href="`/users/${user.id}/workout/music?type=${workoutType}&count=${exerciseCount}&timing=${timing}&exercises=${exerciseIds}`" 
                    class="py-6 px-12 bg-white text-black rounded-full flex items-center gap-4 hover:bg-gray-200 transition-transform hover:scale-105"
                >
                    <span class="text-5xl font-black uppercase tracking-widest">READY</span>
                    <Play class="w-12 h-12 fill-black" />
                </Link>
            </div>
            
            <div class="flex-1 overflow-y-auto pr-4 pb-12 space-y-6">
                <div 
                    v-for="(exercise, index) in exercises" 
                    :key="exercise.id"
                    class="bg-gray-900 border-2 border-gray-800 rounded-3xl p-8 flex flex-col md:flex-row items-center gap-8"
                >
                    <!-- Exercise Number -->
                    <div class="w-20 h-20 rounded-full bg-black border-4 border-white flex items-center justify-center shrink-0">
                        <span class="text-4xl font-black">{{ index + 1 }}</span>
                    </div>

                    <!-- Placeholder Image Area (Since no images are in DB yet) -->
                    <div class="w-full md:w-64 h-48 bg-gray-800 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-600 shrink-0">
                        <span class="text-gray-500 uppercase tracking-widest font-bold">Image / Setup</span>
                    </div>

                    <!-- Exercise Details -->
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-4xl md:text-5xl font-bold uppercase tracking-wider">{{ exercise.title }}</h2>
                    </div>
                </div>

                <div v-if="exercises.length === 0" class="text-center py-20">
                    <p class="text-3xl text-red-500 font-bold uppercase tracking-wider">
                        No exercises found matching your gear and type.
                    </p>
                </div>
            </div>
        </div>

    </GymLayout>
</template>
