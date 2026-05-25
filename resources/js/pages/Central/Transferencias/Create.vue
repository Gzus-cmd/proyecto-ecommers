<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppFormActions from '@/components/app/AppFormActions.vue'

import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Operaciones y Logística', href: '#' },
            { title: 'Transferencias', href: TransferenciaController.index.url() },
            { title: 'Nueva', href: '#' },
        ],
    },
});


interface Lote { id: number; numero_lote: string; cantidad_actual: number; }
interface Producto { id: number; nombre_comercial: string; lotes: Lote[]; }
interface Sede { id: number; nombre: string; }

const props = defineProps<{
    sedes: Sede[],
    productosConLotes: Producto[]
}>()


const form = useForm({
    sede_destino_id: '',
    fecha_envio: new Date().toISOString().split('T')[0],
    observaciones: '',
    detalles: [] as any[]
})


const item = ref({ 
    producto_id: null as number | null, 
    lote_id: null as number | null, 
    cantidad: 1 
})

const lotesDisponibles = computed(() => {
    if (!item.value.producto_id) return []
    const p = props.productosConLotes.find(p => p.id === item.value.producto_id)
    return p ? p.lotes : []
})

const stockMaximo = computed(() => {
    if (!item.value.lote_id) return 999999
    const l = lotesDisponibles.value.find(l => l.id === item.value.lote_id)
    return l ? l.cantidad_actual : 999999
})

function agregar() {
    const p = props.productosConLotes.find(p => p.id === item.value.producto_id)
    const l = lotesDisponibles.value.find(l => l.id === item.value.lote_id)
    
    if (p && l) {
        if (item.value.cantidad > l.cantidad_actual) return alert('La cantidad supera el stock disponible en este lote.')
        if (form.detalles.some(d => d.lote_id === l.id)) return alert('Este lote ya ha sido agregado a la lista.')

        form.detalles.push({
            lote_id: l.id,
            cantidad: item.value.cantidad,
            nombre: p.nombre_comercial,
            lote_num: l.numero_lote
        })
        item.value.lote_id = null
        item.value.cantidad = 1
    }
}

const submit = () => form.post(TransferenciaController.store.url())


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>

<template>

    <AppPageShell title="Nueva Transferencia" variant="wide">
        

        <AppPageHeader 
            title="Nueva Transferencia" 
            subtitle="Defina el destino y la mercadería para el movimiento entre sedes."
            :backUrl="TransferenciaController.index.url()"
        />

        <form @submit.prevent="submit" class="space-y-6">
            

            <AppSectionCard title="Configuración de Destino">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label :class="labelStyle">Sede Destino <span class="text-destructive">*</span></label>
                        <select v-model="form.sede_destino_id" :class="inputStyle">
                            <option value="">Seleccione una sede de la red...</option>
                            <option v-for="s in sedes" :key="s.id" :value="s.id">{{ s.nombre }}</option>
                        </select>
                        <p v-if="form.errors.sede_destino_id" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.sede_destino_id }}</p>
                    </div>
                    <div>
                        <label :class="labelStyle">Fecha Programada</label>
                        <input v-model="form.fecha_envio" type="date" :class="inputStyle" />
                    </div>
                </div>

                <div class="mt-6">
                    <label :class="labelStyle">Observaciones Internas</label>
                    <textarea v-model="form.observaciones" rows="2" class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all resize-none" placeholder="Notas sobre el transporte, prioridad o contenido..."></textarea>
                </div>
            </AppSectionCard>


            <AppSectionCard title="Detalle de Mercadería">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end mb-10">
                    <div class="md:col-span-5">
                        <label :class="labelStyle">Seleccionar Producto</label>
                        <select v-model="item.producto_id" @change="item.lote_id = null" :class="inputStyle">
                            <option :value="null">Buscar producto...</option>
                            <option v-for="p in productosConLotes" :key="p.id" :value="p.id">{{ p.nombre_comercial }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label :class="labelStyle">Lote (Stock disponible)</label>
                        <select v-model="item.lote_id" :disabled="!item.producto_id" :class="inputStyle">
                            <option :value="null">Elegir lote...</option>
                            <option v-for="l in lotesDisponibles" :key="l.id" :value="l.id">
                                {{ l.numero_lote }} - ({{ l.cantidad_actual }} uds)
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label :class="labelStyle">Cant. a Enviar</label>
                        <input v-model.number="item.cantidad" type="number" :max="stockMaximo" min="1" :class="inputStyle" />
                    </div>

                    <div class="md:col-span-1">
                        <button @click.prevent="agregar" type="button" class="w-full h-11 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition font-black text-xl shadow-lg shadow-primary/20">
                            +
                        </button>
                    </div>
                </div>


                <div class="border-t border-border/50 pt-6 overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border/50">
                                <th class="pb-4 px-2">Producto / Lote</th>
                                <th class="pb-4 px-2 text-center">Cant. Solicitada</th>
                                <th class="pb-4 px-2 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/30 text-foreground">
                            <tr v-for="(d, i) in form.detalles" :key="d.lote_id" class="group">
                                <td class="py-5 px-2">
                                    <div class="font-bold text-base leading-tight">{{ d.nombre }}</div>
                                    <div class="text-[10px] text-muted-foreground uppercase tracking-wider font-mono mt-0.5">LOTE: {{ d.lote_num }}</div>
                                </td>
                                <td class="py-5 px-2 text-center">
                                    <span class="bg-primary/10 text-primary px-4 py-1.5 rounded-lg font-black text-sm border border-primary/10">
                                        {{ d.cantidad }}
                                    </span>
                                </td>
                                <td class="py-5 px-2 text-right">
                                    <button @click="form.detalles.splice(i, 1)" type="button" class="text-red-500 font-bold hover:underline uppercase text-[10px] tracking-widest transition-all">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="form.detalles.length === 0">
                                <td colspan="3" class="py-16 text-center text-muted-foreground italic text-sm uppercase tracking-[0.2em] opacity-50">
                                    No se han agregado productos a la lista de carga.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AppSectionCard>


            <AppFormActions 
                :backUrl="TransferenciaController.index.url()" 
                :processing="form.processing" 
                submitLabel="Guardar Transferencia"
            />
        </form>
    </AppPageShell>
</template>