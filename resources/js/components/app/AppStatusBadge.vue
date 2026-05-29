<script setup lang="ts">
const props = defineProps<{ status: string | boolean | number; }>();
const config: Record<string, string> = {
    'Activo': 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
    'Inactivo': 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
    'Sí': 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20',
    'No': 'bg-muted text-muted-foreground border-border',
    'Disponible': 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
};
const label = props.status === true || props.status === 1 ? 'Activo' : 
              props.status === false || props.status === 0 ? 'Inactivo' : props.status;
</script>

<template>
    <span :class="['rounded-full px-2.5 py-0.5 text-[9px] font-black border uppercase tracking-tighter inline-block', config[label as string] || 'bg-muted text-muted-foreground border-border']">
        {{ label }}
    </span>
</template>