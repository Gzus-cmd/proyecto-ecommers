<script setup lang="ts">
import { useForm, Link, Head, router } from '@inertiajs/vue3'
import { Trash2, Truck, AlertTriangle, ArrowLeft, Plus } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'


import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController'


interface Lote { id: number; numero_lote: string; cantidad_actual: number; }
interface Producto { id: number; nombre_comercial: string; lotes: Lote[]; }
interface Sede { id: number; nombre: string; }

const props = defineProps<{ 
    transferencia: any, 
    sedes: Sede[], 
    productosConLotes: Producto[] 
}>()


const form = useForm({
    sede_destino_id: props.transferencia.sede_destino_id,
    fecha_envio: props.transferencia.fecha_envio,
    observaciones: props.transferencia.observaciones || '',
    detalles: [] as any[]
})


const nombreSedeElegida = computed(() => {
    const sede = props.sedes.find(s => s.id === form.sede_destino_id);

    return sede ? sede.nombre : 'Sin destino';
})

onMounted(() => {
    if (props.transferencia.detalles) {
        props.transferencia.detalles.forEach((d: any) => {
            form.detalles.push({
                lote_id: d.lote_id,
                cantidad: d.cantidad,
                nombre: d.lote?.producto?.nombre_comercial || 'Producto',
                lote_num: d.lote?.numero_lote || 'N/A'
            })
        })
    }
})


const item = ref({ producto_id: null as number | null, lote_id: null as number | null, cantidad: 1 })

const lotesDisponibles = computed(() => {
    const p = props.productosConLotes.find(p => p.id === item.value.producto_id)

    return p ? p.lotes : []
})

function agregar() {
    const p = props.productosConLotes.find(p => p.id === item.value.producto_id)
    const l = lotesDisponibles.value.find((l: any) => l.id === item.value.lote_id)

    if (p && l && item.value.cantidad > 0) {
        if (form.detalles.some(d => d.lote_id === l.id)) {
return alert('Ya está en la lista')
}

        form.detalles.push({ lote_id: l.id, cantidad: item.value.cantidad, nombre: p.nombre_comercial, lote_num: l.numero_lote })
        item.value.lote_id = null; item.value.cantidad = 1;
    }
}


const mostrarModalEliminar = ref(false)
const confirmarAnulacion = () => {
    router.delete(TransferenciaController.destroy.url(props.transferencia.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

const submit = () => form.put(TransferenciaController.update.url(props.transferencia.id))


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>

<template>
    <AppPageShell :title="'Editar Transferencia #' + transferencia.id" variant="narrow">
        
        <AppPageHeader 
            title="Editar Transferencia" 
            subtitle="Actualización de datos logísticos y mercadería."
            :backUrl="TransferenciaController.index.url()"
        >
            <template #actions>
                <button
                    v-if="transferencia.estado === 'Pendiente'"
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Anular Documento
                </button>
            </template>
        </AppPageHeader>


        <div v-if="transferencia.estado !== 'Pendiente'" class="bg-amber-500/5 border border-amber-500/20 p-4 rounded-xl flex items-center gap-3 mb-6">
            <AlertTriangle class="size-5 text-amber-500" />
            <p class="text-xs text-foreground/80 font-medium">Esta transferencia está en estado <b>{{ transferencia.estado }}</b>. Los cambios actualizarán el stock.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            
            <AppSectionCard>

                <AppEditContextCard 
                    title="Documento de Movimiento"
                    :subtitle="'Destino: ' + nombreSedeElegida"
                    :itemId="transferencia.id"
                    idLabel="Nro Documento"
                    :icon="Truck"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">
                    <div>
                        <label :class="labelStyle">Sede Destino <span class="text-destructive">*</span></label>
                        <select v-model="form.sede_destino_id" :class="inputStyle">
                            <option v-for="s in sedes" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                        </select>
                        <p v-if="form.errors.sede_destino_id" class="mt-2 text-xs text-red-500">{{ form.errors.sede_destino_id }}</p>
                    </div>
                    <div>
                        <label :class="labelStyle">Fecha Programada</label>
                        <input v-model="form.fecha_envio" type="date" :class="inputStyle" />
                    </div>
                </div>

                <div class="mt-6">
                    <label :class="labelStyle">Observaciones Internas</label>
                    <textarea v-model="form.observaciones" rows="2" :class="inputStyle" class="h-24 resize-none" placeholder="Notas adicionales..."></textarea>
                </div>
            </AppSectionCard>


            <AppSectionCard title="Modificación de Detalle">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-8">
                    <div class="md:col-span-5">
                        <label :class="labelStyle">Producto</label>
                        <select v-model="item.producto_id" @change="item.lote_id = null" :class="inputStyle">
                            <option :value="null">Seleccionar producto...</option>
                            <option v-for="p in productosConLotes" :key="p.id" :value="p.id">{{ p.nombre_comercial }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label :class="labelStyle">Lote / Stock</label>
                        <select v-model="item.lote_id" :disabled="!item.producto_id" :class="inputStyle">
                            <option :value="null">Seleccionar lote...</option>
                            <option v-for="l in lotesDisponibles" :key="l.id" :value="l.id">{{ l.numero_lote }} ({{ l.cantidad_actual }} uds)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label :class="labelStyle">Cant.</label>
                        <input v-model.number="item.cantidad" type="number" min="1" :class="inputStyle" />
                    </div>
                    <div class="md:col-span-1">
                        <button @click.prevent="agregar" type="button" class="w-full h-11 bg-primary text-primary-foreground rounded-lg font-black shadow-lg shadow-primary/20 hover:opacity-90">+</button>
                    </div>
                </div>

                <div class="border-t border-border/50 pt-6">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border/50">
                                <th class="pb-4">Producto / Lote</th>
                                <th class="pb-4 text-center">Cantidad</th>
                                <th class="pb-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/30 text-foreground">
                            <tr v-for="(d, i) in form.detalles" :key="i">
                                <td class="py-5">
                                    <div class="font-bold">{{ d.nombre }}</div>
                                    <div class="text-[10px] text-muted-foreground font-mono uppercase">Lote: {{ d.lote_num }}</div>
                                </td>
                                <td class="py-5 text-center font-black text-primary text-base">{{ d.cantidad }}</td>
                                <td class="py-5 text-right">
                                    <button @click="form.detalles.splice(i,1)" type="button" class="text-red-500 font-bold hover:underline uppercase text-[10px]">Quitar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AppSectionCard>


            <div class="flex justify-end gap-4 pt-4 border-t border-border/50">
                <Link :href="TransferenciaController.index.url()" class="px-8 py-3 rounded-xl border border-border font-bold text-muted-foreground hover:bg-muted transition-all">Cancelar</Link>
                <button type="submit" :disabled="form.processing || !form.detalles.length" class="bg-amber-500 text-white px-10 py-3 rounded-xl font-black shadow-xl hover:bg-amber-600 transition-all uppercase tracking-widest text-xs">
                    Guardar Cambios
                </button>
            </div>
        </form>

        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="`Transferencia #${transferencia.id}`"
            type="documento"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarAnulacion"
        />
    </AppPageShell>
</template>