<script setup lang="ts">
import { Edit3 } from 'lucide-vue-next';

interface Props {
    title: string;
    subtitle: string;
    itemId: string | number;
    idLabel?: string;
}

withDefaults(defineProps<Props>(), {
    idLabel: 'ID Registro'
});
</script>

<template>
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-amber-500/5 p-6 rounded-2xl border border-amber-500/20">
        <div class="flex items-center gap-5">
            <div class="p-4 bg-amber-500/10 rounded-xl text-amber-600 dark:text-amber-400 border border-amber-500/20">
                <Edit3 class="size-7" />
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-600/70 dark:text-amber-400/60 mb-1">
                    {{ title }}
                </p>
                <h2 class="text-2xl font-bold text-foreground leading-none">
                    {{ subtitle }}
                </h2>
            </div>
        </div>

        <div class="text-right bg-background/60 px-5 py-3 rounded-xl border border-border/50 shadow-sm">
            <span class="block text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">
                {{ idLabel }}
            </span>
            <span class="text-primary font-mono font-black text-lg uppercase tracking-tighter">
                #{{ itemId }}
            </span>
        </div>
    </div>
</template>