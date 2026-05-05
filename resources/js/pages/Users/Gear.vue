<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import { Check } from 'lucide-vue-next';

interface WorkoutItem {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
    allGear: WorkoutItem[];
    userGearIds: number[];
}>();

const form = useForm({
    gear_ids: [...props.userGearIds],
});

const toggleGear = (id: number) => {
    const index = form.gear_ids.indexOf(id);
    if (index === -1) {
        form.gear_ids.push(id);
    } else {
        form.gear_ids.splice(index, 1);
    }
};

const submitGear = () => {
    form.put(`/users/${props.user.id}/gear`);
};
</script>

<template>
    <GymLayout title="Select Gear" :show-back-button="true">
        
        <div class="flex flex-col h-full mt-4">
            <h1 class="text-5xl md:text-6xl font-black text-center uppercase tracking-widest mb-2">
                {{ user.name }}'s Gear
            </h1>
            <p class="text-gray-400 text-center text-2xl uppercase tracking-wider mb-10">
                Select the equipment you have available
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12 flex-1 content-start">
                <button 
                    v-for="gear in allGear" 
                    :key="gear.id"
                    @click="toggleGear(gear.id)"
                    class="h-32 flex flex-col items-center justify-center rounded-2xl border-4 transition-all relative overflow-hidden group"
                    :class="[
                        form.gear_ids.includes(gear.id) 
                            ? 'bg-white text-black border-white' 
                            : 'bg-gray-900 border-gray-800 text-white hover:border-gray-600'
                    ]"
                >
                    <div 
                        v-if="form.gear_ids.includes(gear.id)" 
                        class="absolute top-2 right-2 bg-black text-white rounded-full p-1"
                    >
                        <Check class="w-4 h-4" stroke-width="4" />
                    </div>
                    <span class="text-3xl font-bold uppercase tracking-wider text-center px-4">
                        {{ gear.name }}
                    </span>
                </button>
            </div>

            <!-- Save Button -->
            <div class="sticky bottom-0 pb-8 pt-4 bg-gradient-to-t from-black via-black to-transparent">
                <button 
                    @click="submitGear"
                    :disabled="form.processing"
                    class="w-full py-6 bg-white text-black rounded-2xl text-4xl font-black uppercase tracking-widest hover:bg-gray-200 transition-colors disabled:opacity-50"
                >
                    Save & Continue
                </button>
            </div>
        </div>

    </GymLayout>
</template>
