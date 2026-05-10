<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import GymLayout from '@/layouts/GymLayout.vue';

interface Workout {
    id: number;
    type: string;
    start_time: string;
    end_time: string | null;
}

const props = defineProps<{
    lastWorkout: Workout | null;
    workouts: Workout[];
}>();

const parseDate = (dateString: any) => {
    if (!dateString) return new Date();
    if (dateString instanceof Date) return dateString;
    // Ensure it's a string
    let str = String(dateString);
    // Replace space with T and strip microseconds to prevent iOS Safari "Invalid Date"
    const cleaned = str.replace(' ', 'T').replace(/\.\d+/, '');
    return new Date(cleaned);
};

const formatDuration = (start: string, end: string | null) => {
    if (!end) return 'Incomplete';
    const s = parseDate(start).getTime();
    const e = parseDate(end).getTime();
    const diffSeconds = Math.floor((e - s) / 1000);
    
    if (diffSeconds < 0) return '0s';
    
    const minutes = Math.floor(diffSeconds / 60);
    const seconds = diffSeconds % 60;
    
    if (minutes > 0) {
        return `${minutes}m ${seconds}s`;
    }
    return `${seconds}s`;
};

const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = parseDate(dateString);
    if (isNaN(date.getTime())) return ''; // Return empty string if still invalid

    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <GymLayout title="Last workout" :show-back-button="true">
        <Head title="Last workout" />

        <div class="flex flex-col h-full mt-4 max-w-5xl mx-auto w-full gap-12">
            
            <!-- Last Workout Section -->
            <div v-if="lastWorkout && lastWorkout.end_time" class="bg-gray-900 border-4 border-gray-800 rounded-3xl p-12 text-center">
                <h1 class="text-5xl md:text-6xl font-black uppercase tracking-widest text-white mb-6">
                    You did a great job!
                </h1>
                <p class="text-2xl text-gray-400 font-bold uppercase tracking-widest mb-4">
                    Time spent on your last workout:
                </p>
                <div class="text-7xl md:text-[8rem] font-black text-yellow-500 tabular-nums leading-none">
                    {{ formatDuration(lastWorkout.start_time, lastWorkout.end_time) }}
                </div>
            </div>
            <div v-else-if="lastWorkout" class="bg-gray-900 border-4 border-gray-800 rounded-3xl p-12 text-center">
                <h1 class="text-5xl md:text-6xl font-black uppercase tracking-widest text-white mb-6">
                    Workout in progress...
                </h1>
                <p class="text-2xl text-gray-400 font-bold uppercase tracking-widest">
                    Started at: {{ formatDate(lastWorkout.start_time) }}
                </p>
            </div>
            <div v-else class="bg-gray-900 border-4 border-gray-800 rounded-3xl p-12 text-center">
                <h1 class="text-5xl md:text-6xl font-black uppercase tracking-widest text-white mb-6">
                    No workouts yet!
                </h1>
                <p class="text-2xl text-gray-400 font-bold uppercase tracking-widest">
                    Go start a workout to see your stats here.
                </p>
            </div>

            <!-- Workout History -->
            <div v-if="workouts.length > 0" class="bg-gray-900 border-4 border-gray-800 rounded-3xl p-8 overflow-hidden flex flex-col">
                <h2 class="text-3xl font-black uppercase tracking-widest text-white mb-8">
                    Workout History
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-400 border-b-2 border-gray-800 uppercase tracking-wider text-sm">
                                <th class="pb-4 px-4 font-bold">Date</th>
                                <th class="pb-4 px-4 font-bold">Type</th>
                                <th class="pb-4 px-4 font-bold">Duration</th>
                                <th class="pb-4 px-4 font-bold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="workout in workouts" 
                                :key="workout.id"
                                class="border-b border-gray-800 last:border-0 hover:bg-gray-800/50 transition-colors"
                            >
                                <td class="py-4 px-4 text-white font-medium">
                                    {{ formatDate(workout.start_time) }}
                                </td>
                                <td class="py-4 px-4 text-gray-300 capitalize">
                                    {{ workout.type.replace('_', ' ') }}
                                </td>
                                <td class="py-4 px-4 text-yellow-500 font-bold tabular-nums">
                                    {{ formatDuration(workout.start_time, workout.end_time) }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span v-if="workout.end_time" class="px-3 py-1 bg-green-900/50 text-green-400 rounded-full text-xs font-bold uppercase tracking-wider">
                                        Completed
                                    </span>
                                    <span v-else class="px-3 py-1 bg-blue-900/50 text-blue-400 rounded-full text-xs font-bold uppercase tracking-wider">
                                        Incomplete
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </GymLayout>
</template>
