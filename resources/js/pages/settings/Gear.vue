<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Check } from 'lucide-vue-next';
import { edit as editGear } from '@/routes/settings-gear'; // I will need to create this or just hardcode the breadcrumb

interface WorkoutItem {
    id: number;
    name: string;
}

const props = defineProps<{
    allGear: WorkoutItem[];
    userGearIds: number[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Gear settings',
                href: '/settings/gear',
            },
        ],
    },
});

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
    form.put('/settings/gear');
};
</script>

<template>
    <Head title="Gear settings" />

    <h1 class="sr-only">Gear settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Available Gear"
            description="Select the equipment you have available for your workouts"
        />

        <form @submit.prevent="submitGear" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <button 
                    v-for="gear in allGear" 
                    :key="gear.id"
                    type="button"
                    @click="toggleGear(gear.id)"
                    class="h-24 flex flex-col items-center justify-center rounded-xl border-2 transition-all relative overflow-hidden group"
                    :class="[
                        form.gear_ids.includes(gear.id) 
                            ? 'bg-foreground text-background border-foreground' 
                            : 'bg-background border-border text-foreground hover:border-muted-foreground'
                    ]"
                >
                    <div 
                        v-if="form.gear_ids.includes(gear.id)" 
                        class="absolute top-2 right-2 bg-background text-foreground rounded-full p-0.5"
                    >
                        <Check class="w-3 h-3" stroke-width="3" />
                    </div>
                    <span class="text-xl font-bold uppercase tracking-wider text-center px-2">
                        {{ gear.name }}
                    </span>
                </button>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-border">
                <Button type="submit" :disabled="form.processing" data-test="update-gear-button">
                    Save
                </Button>
            </div>
        </form>
    </div>
</template>
