<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
defineProps<{ title: string; subtitle?: string; backUrl?: string; }>();
</script>

<template>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <Link v-if="backUrl" :href="backUrl" 
                  class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground hover:text-primary transition mb-1">
                <ArrowLeft class="size-3" /> Regresar
            </Link>
            <h1 class="text-2xl font-bold text-foreground tracking-tight">{{ title }}</h1>
            <p v-if="subtitle" class="text-sm text-muted-foreground/80 font-medium">{{ subtitle }}</p>
        </div>
        <div class="flex items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>