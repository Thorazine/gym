<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GymLayout from '@/layouts/GymLayout.vue';
import VirtualKeyboard from '@/components/VirtualKeyboard.vue';
import { UserPlus, User as UserIcon } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
}

defineProps<{
    users: User[];
}>();

const isCreateModalOpen = ref(false);

const form = useForm({
    name: '',
});

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
};

const submitCreateUser = () => {
    if (!form.name.trim()) return;
    
    form.post('/users', {
        onSuccess: () => {
            closeCreateModal();
        },
    });
};
</script>

<template>
    <GymLayout title="Select User" :show-back-button="false">
        
        <div class="flex flex-col h-full gap-8 mt-12">
            <h1 class="text-6xl font-black text-center uppercase tracking-widest">Select User</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                <!-- User Buttons -->
                <Link 
                    v-for="user in users" 
                    :key="user.id"
                    :href="'/users/' + user.id + '/login'"
                    method="post"
                    as="button"
                    type="button"
                    class="w-full h-40 flex flex-col items-center justify-center bg-gray-900 border-2 border-white rounded-2xl hover:bg-white hover:text-black transition-all group"
                >
                    <UserIcon class="w-12 h-12 mb-2 group-hover:text-black" />
                    <span class="text-2xl sm:text-3xl md:text-4xl font-bold uppercase tracking-wider line-clamp-2 leading-tight text-center px-4">{{ user.name }}</span>
                </Link>

                <!-- Create Account Button -->
                <button 
                    @click="openCreateModal"
                    class="h-40 flex flex-col items-center justify-center bg-transparent border-2 border-dashed border-gray-500 rounded-2xl hover:border-white hover:text-white text-gray-400 transition-all group"
                >
                    <UserPlus class="w-12 h-12 mb-2" />
                    <span class="text-2xl font-bold uppercase tracking-wider">Create Account</span>
                </button>
            </div>
        </div>

        <!-- Create Account Modal -->
        <div v-if="isCreateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div class="bg-black border-2 border-white rounded-3xl p-8 w-full max-w-4xl shadow-[0_0_50px_rgba(255,255,255,0.1)] flex flex-col items-center">
                
                <h2 class="text-5xl font-black uppercase tracking-wider mb-8">What is your first name?</h2>
                
                <!-- Name Input Display -->
                <div class="w-full max-w-2xl bg-gray-900 border-b-4 border-white text-center p-6 mb-12 rounded-t-xl">
                    <span class="text-6xl font-bold uppercase tracking-widest" :class="{ 'text-gray-600': !form.name }">
                        {{ form.name || 'ENTER NAME...' }}
                    </span>
                    <span v-if="form.errors.name" class="block text-red-500 text-2xl mt-4">{{ form.errors.name }}</span>
                </div>

                <!-- Custom Keyboard -->
                <VirtualKeyboard v-model="form.name" />

                <!-- Action Buttons -->
                <div class="flex w-full justify-between mt-12 gap-6">
                    <button 
                        @click="closeCreateModal"
                        class="flex-1 py-6 border-2 border-white rounded-xl text-3xl font-bold uppercase tracking-wider hover:bg-gray-900 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="submitCreateUser"
                        :disabled="!form.name.trim() || form.processing"
                        class="flex-1 py-6 bg-white text-black rounded-xl text-3xl font-black uppercase tracking-wider hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        OK
                    </button>
                </div>
            </div>
        </div>
        
    </GymLayout>
</template>
