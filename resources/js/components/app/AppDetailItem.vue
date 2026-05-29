<script setup lang="ts">
defineProps<{
    label: string;
    value?: string | number;
    mono?: boolean;
    highlight?: boolean;
}>();
</script>

<template>
    <div class="space-y-1">
        <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-muted-foreground/70">
            {{ label }}
        </label>
        <p :class="[
            'text-foreground leading-tight',
            mono ? 'font-mono tracking-tighter' : 'font-bold',
            highlight ? 'text-primary' : '',
            $attrs.class
        ]">
            <slot>{{ value ?? '—' }}</slot>
        </p>
    </div>
</template>