<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import axios from 'axios';

interface User {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
    workoutType: string;
    exerciseCount: number;
}>();

interface Timing {
    id: number;
    work: number;
    rest: number;
    rounds: number;
}

const timings = ref<Timing[]>([]);
const isLoading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('/api/gym/config');
        timings.value = response.data.timings;
    } catch (error) {
        console.error('Failed to load timings:', error);
    } finally {
        isLoading.value = false;
    }
});

</script>

<template>
    <GymLayout :title="`Select Timing - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-2">
                Select Interval
            </h1>
            <p class="text-gray-400 text-center text-2xl uppercase tracking-wider mb-10">
                Choose your work and rest periods
            </p>
            
            <div class="flex-1 flex flex-col gap-6 pb-12 justify-center max-w-4xl mx-auto w-full">
                <Link 
                    v-for="timing in timings" 
                    :key="timing.id"
                    :href="`/users/${user.id}/workout/setup?type=${workoutType}&count=${exerciseCount}&timing=${timing.id}`"
                    class="py-12 px-6 flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 bg-gray-900 border-4 border-gray-800 rounded-3xl hover:bg-white hover:text-black hover:border-white transition-all group"
                >
                    <div class="flex flex-col items-center">
                        <span class="text-6xl font-black uppercase tracking-widest">{{ timing.work }}s</span>
                        <span class="text-2xl font-bold uppercase tracking-widest text-gray-400 group-hover:text-gray-600">Work</span>
                    </div>
                    
                    <span class="text-4xl text-gray-600 hidden md:block">/</span>
                    
                    <div class="flex flex-col items-center">
                        <span class="text-6xl font-black uppercase tracking-widest">{{ timing.rest }}s</span>
                        <span class="text-2xl font-bold uppercase tracking-widest text-gray-400 group-hover:text-gray-600">Rest</span>
                    </div>
                    
                    <span class="text-4xl text-gray-600 hidden md:block">/</span>
                    
                    <div class="flex flex-col items-center">
                        <span class="text-6xl font-black uppercase tracking-widest">{{ timing.rounds }}</span>
                        <span class="text-2xl font-bold uppercase tracking-widest text-gray-400 group-hover:text-gray-600">Rounds</span>
                    </div>
                </Link>
            </div>
        </div>

    </GymLayout>
</template>
