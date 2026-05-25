<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    backUrl: string;
    processing: boolean;
    submitLabel?: string;
}

withDefaults(defineProps<Props>(), {
    submitLabel: 'Guardar Registro'
});
</script>

<template>
    <div class="flex justify-end gap-4 pt-6 border-t border-border/50 mt-8">

        <Link
            :href="backUrl"
            class="px-6 py-2.5 rounded-lg border border-border text-sm font-bold text-muted-foreground hover:bg-muted transition-all uppercase tracking-widest text-[10px]"
        >
            Cancelar
        </Link>
        

        <button
            type="submit"
            :disabled="processing"
            class="bg-primary text-primary-foreground px-8 py-2.5 rounded-lg text-[11px] font-black shadow-lg shadow-primary/20 hover:opacity-90 disabled:opacity-50 transition-all uppercase tracking-[0.1em]"
        >
            {{ processing ? 'Procesando...' : submitLabel }}
        </button>
    </div>
</template>