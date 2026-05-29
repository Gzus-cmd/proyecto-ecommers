<script setup lang="ts">
defineProps<{ text?: string }>();
</script>

<template>
    <div class="absolute top-0 right-0 p-8 opacity-[0.05] dark:opacity-10 select-none pointer-events-none z-0 flex items-center justify-center overflow-hidden h-full">

        <slot>

            <span v-if="text" class="text-8xl font-black italic text-foreground uppercase tracking-tighter leading-none">
                {{ text }}
            </span>
        </slot>
    </div>
</template>