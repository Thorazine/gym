<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface VideoCategory {
    id: number;
    name: string;
}

interface Video {
    id: number;
    title: string;
    url: string;
    category: VideoCategory;
}

const props = defineProps<{
    videos: Video[];
    categories: VideoCategory[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Youtube Videos',
                href: '/settings/youtube',
            },
        ],
    },
});

const form = useForm({
    url: '',
    video_category_id: '',
});

const submit = () => {
    form.post('/settings/youtube', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const destroy = (id: number) => {
    if (confirm('Are you sure you want to delete this video?')) {
        router.delete(`/settings/youtube/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Youtube Videos" />

    <h1 class="sr-only">Youtube Videos</h1>

    <div class="flex flex-col space-y-12">
        <div class="space-y-6">
            <Heading
                variant="small"
                title="Add Youtube Video"
                description="Add a new workout video from Youtube"
            />

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="url">Youtube URL</Label>
                    <Input
                        id="url"
                        type="url"
                        class="mt-1 block w-full"
                        v-model="form.url"
                        required
                        placeholder="https://www.youtube.com/watch?v=..."
                    />
                    <InputError class="mt-2" :message="form.errors.url" />
                </div>

                <div class="grid gap-2">
                    <Label for="category">Category</Label>
                    <Select v-model="form.video_category_id" required>
                        <SelectTrigger class="w-full mt-1">
                            <SelectValue placeholder="Select a category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id.toString()"
                                >
                                    {{ category.name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.video_category_id" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Save</Button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <Heading
                variant="small"
                title="Existing Videos"
                description="Manage your current Youtube workout videos"
            />

            <div v-if="videos.length === 0" class="text-sm text-muted-foreground">
                No videos found.
            </div>

            <ul v-else class="space-y-4">
                <li
                    v-for="video in videos"
                    :key="video.id"
                    class="flex items-center justify-between p-4 border rounded-lg bg-background"
                >
                    <div class="flex flex-col">
                        <span class="font-medium">{{ video.title }}</span>
                        <span class="text-sm text-muted-foreground">{{ video.category.name }} • {{ video.url }}</span>
                    </div>
                    <Button variant="destructive" size="sm" @click="destroy(video.id)">Remove</Button>
                </li>
            </ul>
        </div>
    </div>
</template>
