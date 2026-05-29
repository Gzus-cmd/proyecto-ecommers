<script setup lang="ts">
interface Props { title?: string; fill?: boolean; noPadding?: boolean; }
defineProps<Props>();
</script>

<template>
    <div class="bg-card rounded-xl border border-border/60 shadow-sm transition-all"
         :class="{ 'flex-1 flex flex-col overflow-hidden': fill }">
        <div v-if="title" class="px-6 py-4 border-b border-border bg-muted/20">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-foreground/70">{{ title }}</h3>
        </div>
        <div :class="[noPadding ? '' : 'p-6', fill ? 'flex-1 overflow-auto' : '']">
            <slot />
        </div>
        <div v-if="$slots.footer" class="mt-auto border-t border-border/50 bg-muted/5 p-4">
            <slot name="footer" />
        </div>
    </div>
</template>