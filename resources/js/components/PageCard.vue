<template>
    <div class="bg-card p-6 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border shadow-sm" :class="{'flex-1 flex flex-col': isTableContainer}">
        <slot />
    </div>
</template>
<script setup lang="ts">
defineProps<{ isTableContainer?: boolean }>();
</script>