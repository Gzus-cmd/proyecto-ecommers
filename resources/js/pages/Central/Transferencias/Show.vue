<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ArrowLeft, Edit3, Trash2, Calendar, MapPin, ClipboardList, Truck, History } from 'lucide-vue-next'
import { ref } from 'vue'


import AppDetailItem from '@/components/app/AppDetailItem.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import AppWatermark from '@/components/app/AppWatermark.vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Operaciones y Logística', href: '#' },
            { title: 'Transferencias', href: TransferenciaController.index.url() },
            { title: 'Ver Detalle', href: '#' },
        ],
    },
});

const props = defineProps<{ transferencia: any }>()


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(TransferenciaController.destroy.url(props.transferencia.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}
</script>

<template>
    <Head :title="`Transferencia #${transferencia.id}`" />

    <AppPageShell :title="`Transferencia #${transferencia.id}`" variant="wide" class="max-w-7xl">
        

        <AppPageHeader 
            title="Detalle de Movimiento" 
            :backUrl="TransferenciaController.index.url()"
        >
            <template #actions>
                <Link
                    v-if="transferencia.estado === 'Pendiente'"
                    :href="TransferenciaController.edit.url(transferencia.id)"
                    class="bg-primary/10 text-primary border border-primary/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all flex items-center gap-2 shadow-sm"
                >
                    <Edit3 class="size-3.5" /> Editar Datos
                </Link>
                <button
                    v-if="transferencia.estado === 'Pendiente'"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2 shadow-sm"
                >
                    <Trash2 class="size-3.5" /> Anular Envío
                </button>
            </template>
        </AppPageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            

            <div class="lg:col-span-8 space-y-6">
                

                <AppSectionCard class="relative min-h-50 flex flex-col justify-center">
                    <AppWatermark>
                        <AppLogoIcon class="size-64 fill-current text-foreground" />
                    </AppWatermark>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <AppStatusBadge :status="transferencia.estado" />
                            <div class="text-right">
                                <span class="block text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">Fecha Programada</span>
                                <span class="text-foreground font-mono font-bold">{{ transferencia.fecha_envio }}</span>
                            </div>
                        </div>

                        <h1 class="text-4xl font-black text-foreground leading-tight uppercase tracking-tighter">
                            TRANSFERENCIA #{{ transferencia.id }}
                        </h1>
                        
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-border/50 pt-8">
                            <AppDetailItem label="Sede de Destino">
                                <div class="flex items-center gap-2">
                                    <MapPin class="size-4 text-primary" />
                                    <span class="text-xl font-bold text-white">{{ transferencia.sede_destino.nombre }}</span>
                                </div>
                            </AppDetailItem>
                            <AppDetailItem label="Tipo de Operación" value="Distribución entre Sedes" highlight />
                        </div>
                    </div>
                </AppSectionCard>


                <AppSectionCard>
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-muted/30 rounded-xl text-muted-foreground"><ClipboardList class="size-5" /></div>
                        <AppDetailItem label="Observaciones e Instrucciones">
                            <p class="text-foreground/80 leading-relaxed italic font-medium">
                                {{ transferencia.observaciones || 'No se registraron notas adicionales para este movimiento.' }}
                            </p>
                        </AppDetailItem>
                    </div>
                </AppSectionCard>


                <AppSectionCard noPadding title="Mercadería en Tránsito">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="bg-muted/20 text-muted-foreground font-black uppercase text-[9px] tracking-widest">
                            <tr>
                                <th class="px-8 py-4 w-2/3">Producto / Especificaciones del Lote</th>
                                <th class="px-8 py-4 text-right">Cant. Solicitada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/30">
                            <tr v-for="d in transferencia.detalles" :key="d.id" class="hover:bg-muted/5 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="text-base font-bold text-foreground">{{ d.lote.producto.nombre_comercial }}</div>
                                    <div class="text-[10px] text-muted-foreground uppercase tracking-wider mt-1 font-mono">LOTE: {{ d.lote.numero_lote }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="bg-primary/10 text-primary px-4 py-1.5 rounded-lg font-black text-sm border border-primary/10">
                                        {{ d.cantidad }} uds.
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </AppSectionCard>
            </div>


            <div class="lg:col-span-4 space-y-6">
                

                <AppSectionCard v-if="transferencia.movimientos?.length" noPadding title="Trazabilidad Técnica">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-3 text-emerald-400 border-b border-border/30 pb-4 mb-2">
                            <History class="size-4" />
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em]">Rastro de Inventario</h3>
                        </div>

                        <div class="space-y-4">
                            <div v-for="m in transferencia.movimientos" :key="m.id" 
                                 class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl space-y-3">
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-emerald-400 uppercase tracking-tighter">Lote: {{ m.lote.numero_lote }}</span>
                                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                </div>

                                <div class="flex justify-between items-end">
                                    <div class="space-y-1">
                                        <span class="block text-[8px] font-black text-muted-foreground uppercase tracking-widest">Variación</span>
                                        <p class="font-mono font-bold text-emerald-400 text-sm tracking-tighter">
                                            {{ m.stock_antes }} → {{ m.stock_despues }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-[8px] font-black text-muted-foreground uppercase tracking-widest">Responsable</span>
                                        <p class="text-[10px] font-bold text-foreground/70">{{ m.usuario.name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </AppSectionCard>


                <div v-else class="bg-primary/5 border border-primary/10 p-12 rounded-3xl flex flex-col items-center justify-center opacity-30 grayscale text-center">
                    <Truck class="size-16 text-primary mb-4" />
                    <p class="text-[10px] font-black uppercase tracking-widest">En espera de envío</p>
                </div>
            </div>
        </div>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="`Transferencia #${transferencia.id}`"
            type="movimiento logístico"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>