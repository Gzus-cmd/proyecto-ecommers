<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Trash2, Edit3, ArrowLeft, Calendar, Package, Database, Banknote, History } from 'lucide-vue-next'
import { ref } from 'vue'


import AppDetailItem from '@/components/app/AppDetailItem.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import AppWatermark from '@/components/app/AppWatermark.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as LoteController from '@/actions/App/Http/Controllers/Central/LoteController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión de Inventario', href: '#' },
            { title: 'Lotes y Stock', href: LoteController.index.url() },
            { title: 'Ficha de Lote', href: '#' },
        ],
    },
});

const props = defineProps<{ lote: any }>()
const mostrarModalEliminar = ref(false)

const confirmarEliminacion = () => {
    router.delete(LoteController.destroy.url(props.lote.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}
</script>

<template>
    <Head :title="`Lote: ${lote.numero_lote}`" />

    <AppPageShell :title="`Lote: ${lote.numero_lote}`" variant="wide" class="max-w-7xl">
        

        <AppPageHeader 
            title="Detalle de Lote" 
            :backUrl="LoteController.index.url()"
        >
            <template #actions>
                <Link :href="LoteController.edit.url(props.lote.id)" 
                      class="bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <Edit3 class="size-3.5" /> Editar Lote
                </Link>
                <button @click="mostrarModalEliminar = true" 
                        class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <Trash2 class="size-3.5" /> Eliminar Registro
                </button>
            </template>
        </AppPageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            

            <div class="lg:col-span-8 space-y-6">
                

                <AppSectionCard class="relative">
                    <AppWatermark text="STOCK" />
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-8">
                            <div class="flex gap-2">
                                <AppStatusBadge :status="lote.estado" />
                                <span class="bg-primary/10 text-primary px-3 py-1 rounded-lg text-[9px] font-black border border-primary/20 uppercase">
                                    ID: #{{ lote.id }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-10">
                            <h1 class="text-4xl font-black text-foreground leading-none mb-2 uppercase tracking-tighter font-mono">
                                {{ lote.numero_lote }}
                            </h1>
                            <div class="flex items-center gap-2 text-primary">
                                <Package class="size-5" />
                                <p class="text-xl font-bold italic">
                                    {{ lote.producto?.nombre_comercial }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 border-t border-border/50 pt-8">
                            <AppDetailItem label="SKU de Producto" :value="lote.producto?.sku" mono />
                            <AppDetailItem label="Cantidad Inicial" :value="lote.cantidad_inicial + ' uds.'" />
                            <AppDetailItem label="Costo Unitario" :value="'S/ ' + Number(lote.costo_unitario).toFixed(2)" highlight />
                            <AppDetailItem label="Registro Sistema" :value="lote.fecha_ingreso" />
                        </div>
                    </div>
                </AppSectionCard>


                <AppSectionCard>
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-primary mb-10 flex items-center gap-2">
                        <Calendar class="size-4" /> Línea de Tiempo del Lote
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">

                        <div class="hidden md:block absolute top-1/2 left-0 w-full h-px bg-border/40 -translate-y-1/2 z-0"></div>

                        <div class="relative z-10 bg-card pr-4">
                            <AppDetailItem label="Fabricación" :value="lote.fecha_fabricacion || 'No registrada'">
                                <p class="text-foreground font-bold text-base">{{ lote.fecha_fabricacion || '—' }}</p>
                                <div class="w-2.5 h-2.5 bg-muted-foreground/30 rounded-full mt-3 border-2 border-card"></div>
                            </AppDetailItem>
                        </div>

                        <div class="relative z-10 bg-card px-4">
                            <AppDetailItem label="Ingreso a Almacén" :value="lote.fecha_ingreso">
                                <p class="text-foreground font-bold text-base">{{ lote.fecha_ingreso }}</p>
                                <div class="w-2.5 h-2.5 bg-primary rounded-full mt-3 border-2 border-card"></div>
                            </AppDetailItem>
                        </div>

                        <div class="relative z-10 bg-card pl-4">
                            <AppDetailItem label="Vencimiento" :value="lote.fecha_vencimiento">
                                <p class="text-red-500 dark:text-red-400 font-black text-base">{{ lote.fecha_vencimiento }}</p>
                                <div class="w-2.5 h-2.5 bg-red-500 rounded-full mt-3 border-2 border-card animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.4)]"></div>
                            </AppDetailItem>
                        </div>
                    </div>
                </AppSectionCard>
            </div>


            <div class="lg:col-span-4 space-y-6">
                

                <AppSectionCard variant="success" class="border-emerald-500/20 bg-emerald-500/5">
                    <div class="text-center py-4">
                        <div class="inline-flex p-4 bg-emerald-500/10 rounded-2xl text-emerald-500 mb-4 border border-emerald-500/20">
                            <Database class="size-8" />
                        </div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400/70 mb-2">
                            Stock Actual Disponible
                        </label>
                        <p class="text-6xl font-black text-foreground tracking-tighter">
                            {{ lote.cantidad_actual }}
                        </p>
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400/60 mt-2 uppercase">Unidades en estantería</p>
                    </div>
                </AppSectionCard>


                <AppSectionCard title="Información Financiera">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-primary/10 rounded-xl text-primary"><Banknote class="size-5" /></div>
                        <div>
                            <label class="block text-[9px] font-black uppercase tracking-widest text-muted-foreground">Inversión del Lote</label>
                            <p class="text-xl font-black text-foreground">S/ {{ (lote.cantidad_actual * lote.costo_unitario).toLocaleString('es-PE', { minimumFractionDigits: 2 }) }}</p>
                        </div>
                    </div>
                </AppSectionCard>


                <AppSectionCard noPadding title="Trazabilidad de Sistema">
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center border-b border-border/30 pb-3">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase">Creado</span>
                            <span class="text-[11px] font-mono text-foreground/70">{{ lote.created_at || '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase">Actualizado</span>
                            <span class="text-[11px] font-mono text-foreground/70">{{ lote.updated_at || '—' }}</span>
                        </div>
                    </div>
                </AppSectionCard>
            </div>
        </div>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar" 
            :itemName="lote.numero_lote" 
            type="lote de inventario" 
            @close="mostrarModalEliminar = false" 
            @confirm="confirmarEliminacion" 
        />

    </AppPageShell>
</template>