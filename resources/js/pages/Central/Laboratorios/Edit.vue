<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2, FlaskConical } from 'lucide-vue-next'
import { ref } from 'vue'


import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as LaboratorioController from '@/actions/App/Http/Controllers/Central/LaboratorioController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Laboratorios', href: LaboratorioController.index.url() },
            { title: 'Editar Laboratorio', href: '#' },
        ],
    },
});

interface Laboratorio {
    id: number
    nombre: string
    pais: string | null
}
 
const props = defineProps<{ laboratorio: Laboratorio }>()
 
const form = useForm({
    nombre: props.laboratorio.nombre,
    pais: props.laboratorio.pais ?? '',
})


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(LaboratorioController.destroy.url(props.laboratorio.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}
 
function submit() {
    form.put(LaboratorioController.update.url(props.laboratorio.id))
}


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>
 
<template>
    <AppPageShell :title="'Editar: ' + laboratorio.nombre" variant="narrow">


        <AppPageHeader 
            title="Editar Laboratorio" 
            subtitle="Actualice la información del fabricante farmacéutico."
            :backUrl="LaboratorioController.index.url()"
        >
            <template #actions>
                <button
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Eliminar Registro
                </button>
            </template>
        </AppPageHeader>


        <AppSectionCard>

            <AppEditContextCard 
                title="Editando Laboratorio Fabricante"
                :subtitle="laboratorio.nombre"
                :itemId="laboratorio.id"
                idLabel="ID de Sistema"
                :icon="FlaskConical"
            />

            <form @submit.prevent="submit" class="space-y-8">
                <div>
                    <label :class="labelStyle">
                        Nombre del Laboratorio <span class="text-destructive">*</span>
                    </label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        placeholder="Ej: Bayer, Pfizer..."
                        :class="inputStyle"
                    />
                    <p v-if="form.errors.nombre" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.nombre }}</p>
                </div>
 
                <div>
                    <label :class="labelStyle">
                        País de Origen / Sede Central
                    </label>
                    <input
                        v-model="form.pais"
                        type="text"
                        placeholder="Ej: Alemania, EE.UU..."
                        :class="inputStyle"
                    />
                    <p v-if="form.errors.pais" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.pais }}</p>
                </div>
 

                <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                    <Link
                        :href="LaboratorioController.index.url()"
                        class="px-6 py-2.5 rounded-lg border border-border text-sm font-bold text-muted-foreground hover:bg-muted transition"
                    >
                        Anular Cambios
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-amber-500 text-white px-8 py-2.5 rounded-lg text-sm font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-wider"
                    >
                        {{ form.processing ? 'Sincronizando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="laboratorio.nombre"
            type="laboratorio"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>