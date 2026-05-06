<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

interface SoundcloudTrack {
    id: number;
    title: string;
    url: string;
}

const props = defineProps<{
    tracks: SoundcloudTrack[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Soundcloud Tracks',
                href: '/settings/soundcloud',
            },
        ],
    },
});

const form = useForm({
    url: '',
});

const submit = () => {
    form.post('/settings/soundcloud', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const destroy = (id: number) => {
    if (confirm('Are you sure you want to delete this track?')) {
        router.delete(`/settings/soundcloud/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Soundcloud Tracks" />

    <h1 class="sr-only">Soundcloud Tracks</h1>

    <div class="flex flex-col space-y-12">
        <div class="space-y-6">
            <Heading
                variant="small"
                title="Add Soundcloud Track"
                description="Add a new workout track from Soundcloud"
            />

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="url">Soundcloud URL</Label>
                    <Input
                        id="url"
                        type="url"
                        class="mt-1 block w-full"
                        v-model="form.url"
                        required
                        placeholder="https://soundcloud.com/..."
                    />
                    <InputError class="mt-2" :message="form.errors.url" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Save</Button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <Heading
                variant="small"
                title="Existing Tracks"
                description="Manage your current Soundcloud workout tracks"
            />

            <div v-if="tracks.length === 0" class="text-sm text-muted-foreground">
                No tracks found.
            </div>

            <ul v-else class="space-y-4">
                <li
                    v-for="track in tracks"
                    :key="track.id"
                    class="flex items-center justify-between p-4 border rounded-lg bg-background"
                >
                    <div class="flex flex-col overflow-hidden">
                        <span class="font-medium truncate">{{ track.title }}</span>
                        <span class="text-sm text-muted-foreground truncate">{{ track.url }}</span>
                    </div>
                    <Button variant="destructive" size="sm" @click="destroy(track.id)" class="ml-4 shrink-0">Remove</Button>
                </li>
            </ul>
        </div>
    </div>
</template>
