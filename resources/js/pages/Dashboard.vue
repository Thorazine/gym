<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';

const props = defineProps<{
    totalWorkouts: number;
    totalTimeSeconds: number;
}>();

const formatDuration = (totalSeconds: number) => {
    if (totalSeconds <= 0) return '0s';
    
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    
    let result = '';
    if (hours > 0) result += `${hours}h `;
    if (minutes > 0) result += `${minutes}m `;
    result += `${seconds}s`;
    
    return result.trim();
};
</script>

<template>
    <GymLayout title="Dashboard" :show-back-button="true">
        <Head title="Dashboard" />

        <div class="flex flex-col h-full mt-4 max-w-5xl mx-auto w-full gap-12">
            
            <div class="bg-gray-900 border-4 border-gray-800 rounded-3xl p-12 text-center">
                <h1 class="text-5xl md:text-6xl font-black uppercase tracking-widest text-white mb-6">
                    Your Stats
                </h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
                    <div class="bg-gray-800 rounded-2xl p-8 border-4 border-gray-700">
                        <p class="text-xl md:text-2xl text-gray-400 font-bold uppercase tracking-widest mb-4">
                            Total Workouts
                        </p>
                        <div class="text-5xl md:text-7xl font-black text-yellow-500 tabular-nums leading-none">
                            {{ totalWorkouts }}
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-2xl p-8 border-4 border-gray-700">
                        <p class="text-xl md:text-2xl text-gray-400 font-bold uppercase tracking-widest mb-4">
                            Total Time Spent
                        </p>
                        <div class="text-4xl md:text-6xl font-black text-yellow-500 tabular-nums leading-none">
                            {{ formatDuration(totalTimeSeconds) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </GymLayout>
</template>
