<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';

interface User {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
    workoutType: string;
}>();

const counts = Array.from({ length: 10 }, (_, i) => i + 1);

</script>

<template>
    <GymLayout :title="`Select Exercises - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-2">
                How Many Exercises?
            </h1>
            <p class="text-gray-400 text-center text-2xl uppercase tracking-wider mb-10">
                Select between 1 and 10
            </p>
            
            <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 pb-12 content-center">
                <Link 
                    v-for="count in counts" 
                    :key="count"
                    :href="`/users/${user.id}/workout/timing?type=${workoutType}&count=${count}`"
                    class="aspect-square flex items-center justify-center bg-gray-900 border-4 border-gray-800 rounded-3xl hover:bg-white hover:text-black hover:border-white transition-all group"
                >
                    <span class="text-7xl font-black uppercase tracking-widest">
                        {{ count }}
                    </span>
                </Link>
            </div>
        </div>

    </GymLayout>
</template>
