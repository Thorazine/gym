<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { User as UserIcon } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
}

defineProps<{
    users: User[];
}>();

defineOptions({
    layout: null,
});
</script>

<template>
    <Head title="Log in" />

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div class="bg-black border-2 border-white rounded-3xl p-8 w-full max-w-4xl shadow-[0_0_50px_rgba(255,255,255,0.1)] flex flex-col items-center">
            <h2 class="text-5xl font-black uppercase tracking-wider mb-8 text-white">Select User to Login</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 w-full max-h-[60vh] overflow-y-auto pr-2 pb-4">
                <Link 
                    v-for="user in users" 
                    :key="user.id"
                    :href="'/users/' + user.id + '/login'"
                    method="post"
                    as="button"
                    type="button"
                    class="w-full h-40 flex flex-col items-center justify-center bg-gray-900 border-2 border-white rounded-2xl hover:bg-white hover:text-black transition-all group text-white"
                >
                    <UserIcon class="w-12 h-12 mb-2 group-hover:text-black" />
                    <span class="text-4xl font-bold uppercase tracking-wider">{{ user.name }}</span>
                </Link>
            </div>
            <div v-if="!users || users.length === 0" class="text-white text-2xl uppercase tracking-wider mt-4">
                No users found. Please create one on the main screen.
            </div>
        </div>
    </div>
</template>
