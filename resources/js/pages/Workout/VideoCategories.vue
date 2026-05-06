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
    slug: string;
}

const props = defineProps<{
    user: User;
    categories: VideoCategory[];
}>();

</script>

<template>
    <GymLayout :title="`Video Categories - ${user.name}`" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-10">
                Choose Category
            </h1>
            
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 pb-12 content-center max-w-4xl mx-auto w-full">
                <Link 
                    v-for="category in categories" 
                    :key="category.id"
                    :href="`/users/${user.id}/workout/video-categories/${category.id}`"
                    class="h-20 md:h-32 flex items-center justify-center bg-gray-900 border-4 border-gray-800 rounded-3xl hover:bg-white hover:text-black hover:border-white transition-all group"
                >
                    <span class="text-3xl md:text-4xl font-black uppercase tracking-widest px-6 text-center">
                        {{ category.name }}
                    </span>
                </Link>
            </div>
            
            <div v-if="categories.length === 0" class="text-center text-gray-400 text-2xl mt-10">
                No video categories available. Please add some via the terminal command.
            </div>
        </div>

    </GymLayout>
</template>
