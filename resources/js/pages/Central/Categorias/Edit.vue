<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2, Tags } from 'lucide-vue-next'
import { ref } from 'vue'


import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as CategoriaController from '@/actions/App/Http/Controllers/Central/CategoriaController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Entidades Maestras', href: '#' },
            { title: 'Categorías', href: CategoriaController.index.url() },
            { title: 'Editar', href: '#' },
        ],
    },
});

interface Categoria {
    id: number
    nombre: string
    descripcion: string | null
}
 
const props = defineProps<{ categoria: Categoria }>()
 
const form = useForm({
    nombre: props.categoria.nombre,
    descripcion: props.categoria.descripcion ?? '',
})


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(CategoriaController.destroy.url(props.categoria.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

function submit() {
    form.put(CategoriaController.update.url(props.categoria.id))
}


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>
 
<template>
    <AppPageShell :title="'Editar: ' + categoria.nombre" variant="narrow">


        <AppPageHeader 
            title="Editar Categoría" 
            :backUrl="CategoriaController.index.url()"
        >
            <template #actions>
                <button
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Borrar Categoría
                </button>
            </template>
        </AppPageHeader>


        <AppSectionCard>

            <AppEditContextCard 
                title="Editando Categoría de Productos"
                :subtitle="categoria.nombre"
                :itemId="categoria.id"
                idLabel="ID de Registro"
                :icon="Tags"
            />

            <form @submit.prevent="submit" class="space-y-8">
                <div>
                    <label :class="labelStyle">
                        Nombre de la Categoría <span class="text-destructive">*</span>
                    </label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        placeholder="Ej: Antibióticos"
                        :class="inputStyle"
                    />
                    <p v-if="form.errors.nombre" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.nombre }}</p>
                </div>
 
                <div>
                    <label :class="labelStyle">Descripción Detallada</label>
                    <textarea
                        v-model="form.descripcion"
                        rows="4"
                        placeholder="Describa el uso de esta familia de productos..."
                        class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all resize-none"
                    />
                    <p v-if="form.errors.descripcion" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.descripcion }}</p>
                </div>
 

                <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                    <Link
                        :href="CategoriaController.index.url()"
                        class="px-6 py-2.5 rounded-lg border border-border text-sm font-bold text-muted-foreground hover:bg-muted transition"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-amber-500 text-white px-10 py-2.5 rounded-lg text-sm font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-widest"
                    >
                        {{ form.processing ? 'Procesando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="categoria.nombre"
            type="categoría"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>