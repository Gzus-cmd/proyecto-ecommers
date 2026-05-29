<template>
    <!-- Si es index se estira (h-full), si es form se centra (mx-auto) -->
    <div :class="[isIndex ? 'flex h-full flex-1 flex-col gap-4 p-4' : 'p-4 max-w-4xl mx-auto w-full space-y-4']">
        <slot />
    </div>
</template>
<script setup lang="ts">
defineProps<{ isIndex?: boolean }>();
</script>