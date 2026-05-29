<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2, Layers } from 'lucide-vue-next'
import { ref } from 'vue'


import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as LoteController from '@/actions/App/Http/Controllers/Central/LoteController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión de Inventario', href: '#' },
            { title: 'Lotes y Stock', href: LoteController.index.url() },
            { title: 'Editar Lote', href: '#' },
        ],
    },
});

interface Lote {
    id: number; producto_id: number; numero_lote: string;
    fecha_fabricacion: string | null; fecha_ingreso: string; fecha_vencimiento: string;
    cantidad_inicial: number; cantidad_actual: number; costo_unitario: number;
    estado: string;
}
interface Producto { id: number; nombre_comercial: string; sku: string }

const props = defineProps<{
    lote: Lote
    productos: Producto[]
    estados: string[]
}>()


const form = useForm({
    producto_id:       props.lote.producto_id,
    numero_lote:       props.lote.numero_lote,
    fecha_fabricacion: props.lote.fecha_fabricacion ?? '',
    fecha_ingreso:     props.lote.fecha_ingreso,
    fecha_vencimiento: props.lote.fecha_vencimiento,
    cantidad_inicial:  props.lote.cantidad_inicial,
    cantidad_actual:   props.lote.cantidad_actual,
    costo_unitario:    props.lote.costo_unitario,
    estado:            props.lote.estado,
})


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(LoteController.destroy.url(props.lote.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

const submit = () => {
    form.put(LoteController.update.url(props.lote.id))
}


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>

<template>
    <AppPageShell :title="'Editar Lote: ' + lote.numero_lote" variant="narrow">
        

        <AppPageHeader 
            title="Editar Lote" 
            subtitle="Actualice la información de stock, fechas o estado."
            :backUrl="LoteController.index.url()"
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
                title="Editando Lote de Inventario"
                :subtitle="lote.numero_lote"
                :itemId="lote.id"
                idLabel="ID Interno"
                :icon="Layers"
            />

            <form @submit.prevent="submit" class="space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Producto Asignado <span class="text-destructive">*</span></label>
                        <select v-model="form.producto_id" :class="inputStyle">
                            <option v-for="p in productos" :key="p.id" :value="p.id">
                                [{{ p.sku }}] {{ p.nombre_comercial }}
                            </option>
                        </select>
                        <p v-if="form.errors.producto_id" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.producto_id }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label :class="labelStyle">Nro. de Lote <span class="text-destructive">*</span></label>
                            <input v-model="form.numero_lote" type="text" :class="inputStyle" />
                            <p v-if="form.errors.numero_lote" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.numero_lote }}</p>
                        </div>
                        <div>
                            <label :class="labelStyle">Estado</label>
                            <select v-model="form.estado" :class="inputStyle">
                                <option v-for="e in estados" :key="e" :value="e">{{ e }}</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-muted/10 rounded-xl border border-border/50">
                    <div>
                        <label :class="labelStyle">Fabricación</label>
                        <input v-model="form.fecha_fabricacion" type="date" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Ingreso <span class="text-destructive">*</span></label>
                        <input v-model="form.fecha_ingreso" type="date" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Vencimiento <span class="text-destructive">*</span></label>
                        <input v-model="form.fecha_vencimiento" type="date" :class="inputStyle" />
                        <p v-if="form.errors.fecha_vencimiento" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.fecha_vencimiento }}</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label :class="labelStyle">Cant. Inicial</label>
                        <input v-model.number="form.cantidad_inicial" type="number" min="1" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Cant. Actual <span class="text-destructive">*</span></label>
                        <input v-model.number="form.cantidad_actual" type="number" min="0" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Costo Unit. (S/)</label>
                        <input v-model.number="form.costo_unitario" type="number" min="0" step="0.01" :class="inputStyle" />
                    </div>
                </div>


                <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                    <Link
                        :href="LoteController.index.url()"
                        class="px-6 py-2.5 rounded-lg border border-border text-sm font-bold text-muted-foreground hover:bg-muted transition"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-amber-500 text-white px-10 py-2.5 rounded-lg text-sm font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-widest"
                    >
                        {{ form.processing ? 'Guardando...' : 'Actualizar Lote' }}
                    </button>
                </div>
            </form>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="lote.numero_lote"
            type="lote de inventario"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>