<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';

interface Props {
    title?: string;
    showBackButton?: boolean;
}

withDefaults(defineProps<Props>(), {
    title: 'Gym',
    showBackButton: true,
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="title" />
    
    <div class="min-h-screen bg-black text-white p-6 flex flex-col">
        <!-- Header -->
        <header v-if="showBackButton" class="mb-8 flex items-center">
            <button 
                @click="goBack" 
                class="flex items-center gap-2 text-2xl font-bold hover:text-gray-300 transition-colors uppercase tracking-wider"
            >
                <ChevronLeft class="w-8 h-8" />
                Back
            </button>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col w-full max-w-7xl mx-auto">
            <slot />
        </main>
    </div>
</template>
