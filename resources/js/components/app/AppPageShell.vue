<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
interface Props {
    title: string;
    variant?: 'wide' | 'narrow' | 'full';
}
const props = withDefaults(defineProps<Props>(), { variant: 'full' });
</script>

<template>
    <Head :title="title" />
    <div class="flex flex-col gap-6 p-6 md:px-10 md:py-8 text-foreground transition-colors duration-300"
         :class="[
            variant === 'full' ? 'h-full flex-1 overflow-x-hidden' : '',
            variant === 'wide' ? 'max-w-7xl mx-auto w-full' : '',
            variant === 'narrow' ? 'max-w-4xl mx-auto w-full' : ''
         ]">
        <slot />
    </div>
</template>