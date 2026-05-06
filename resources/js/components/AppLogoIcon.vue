<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        src="/favicon.png"
        :class="className"
        v-bind="$attrs"
        alt="Gym Logo"
    />
</template>
