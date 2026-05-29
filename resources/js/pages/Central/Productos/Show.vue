<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Trash2, Edit3, ArrowLeft, FlaskConical, Truck, Package, Info } from 'lucide-vue-next'
import { ref } from 'vue'


import AppDetailItem from '@/components/app/AppDetailItem.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import AppWatermark from '@/components/app/AppWatermark.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as ProductoMaestroController from '@/actions/App/Http/Controllers/Central/ProductoMaestroController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión de Inventario', href: '#' },
            { title: 'Productos Maestro', href: ProductoMaestroController.index.url() },
            { title: 'Ficha Técnica', href: '#' },
        ],
    },
});

const props = defineProps<{ producto: any }>()
const mostrarModalEliminar = ref(false)

const confirmarEliminacion = () => {
    router.delete(ProductoMaestroController.destroy.url(props.producto.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}
</script>

<template>
    <Head :title="producto.nombre_comercial" />


    <AppPageShell :title="producto.nombre_comercial" variant="wide" class="max-w-7xl">
        

        <AppPageHeader 
            title="Ficha del Producto" 
            :backUrl="ProductoMaestroController.index.url()"
        >
            <template #actions>
                <Link :href="ProductoMaestroController.edit.url(props.producto.id)" 
                      class="bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <Edit3 class="size-3.5" /> Editar Ficha
                </Link>
                <button @click="mostrarModalEliminar = true" 
                        class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <Trash2 class="size-3.5" /> Eliminar Producto
                </button>
            </template>
        </AppPageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            

            <div class="lg:col-span-8 space-y-6">
                

                <AppSectionCard class="relative">
                    <AppWatermark text="PHARMA" />
                    
                    <div class="relative z-10">
                        <div class="flex gap-2 mb-6">
                            <AppStatusBadge :status="producto.activo" />
                            <AppStatusBadge v-if="producto.requiere_receta" status="Sí" />
                        </div>


                        <div class="mb-10">
                            <h1 class="text-4xl font-black text-foreground leading-none mb-2 uppercase tracking-tighter">
                                {{ producto.nombre_comercial }}
                            </h1>
                            <p class="text-xl text-primary font-medium italic">
                                {{ producto.nombre_generico ?? 'Sin nombre genérico' }}
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 border-t border-border/50 pt-8">
                            <AppDetailItem label="SKU Identificador" :value="producto.sku" mono highlight />
                            <AppDetailItem label="Categoría" :value="producto.categoria?.nombre" />
                            <AppDetailItem label="Stock Mínimo" :value="producto.stock_minimo + ' uds.'" />
                            <AppDetailItem label="Reg. Sanitario" :value="producto.registro_sanitario" />
                        </div>
                    </div>
                </AppSectionCard>


                <AppSectionCard>
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-primary mb-8 flex items-center gap-2">
                        <FlaskConical class="size-4" /> Especificaciones Técnicas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <AppDetailItem label="Concentración" :value="producto.concentracion" class="text-lg" />
                        <AppDetailItem label="Forma Farmacéutica" :value="producto.forma_farmaceutica" class="text-lg" />
                        <AppDetailItem label="Unidad de Medida" :value="producto.unidad_medida" class="text-lg" />
                    </div>
                    
                    <div v-if="producto.descripcion" class="mt-10 pt-6 border-t border-border/30">
                        <div class="flex items-center gap-2 mb-3">
                            <Info class="size-3 text-muted-foreground" />
                            <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground">Descripción y Notas</label>
                        </div>
                        <p class="text-foreground/80 leading-relaxed italic text-sm bg-muted/5 p-4 rounded-lg border border-border/50">
                            {{ producto.descripcion }}
                        </p>
                    </div>
                </AppSectionCard>
            </div>


            <div class="lg:col-span-4 space-y-6">
                

                <AppSectionCard title="Origen y Suministro">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-primary/10 rounded-xl text-primary"><Package class="size-5" /></div>
                            <AppDetailItem label="Laboratorio Fabricante">
                                <p class="text-foreground font-bold text-base leading-tight">{{ producto.laboratorio?.nombre }}</p>
                                <span class="text-[10px] text-muted-foreground uppercase tracking-widest">{{ producto.laboratorio?.pais }}</span>
                            </AppDetailItem>
                        </div>
                        
                        <div class="flex items-start gap-4 pt-6 border-t border-border/50">
                            <div class="p-3 bg-primary/10 rounded-xl text-primary"><Truck class="size-5" /></div>
                            <AppDetailItem label="Proveedor Principal" :value="producto.proveedor?.razon_social" />
                        </div>
                    </div>
                </AppSectionCard>


                <AppSectionCard noPadding title="Disponibilidad por Lotes">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-muted/10 text-[9px] font-black uppercase text-muted-foreground/60 tracking-widest border-b border-border/50">
                            <tr>
                                <th class="px-6 py-4">Lote / Vence</th>
                                <th class="px-6 py-4 text-right">Cant.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/30 text-foreground">
                            <tr v-for="lote in producto.lotes" :key="lote.id" class="hover:bg-muted/10 transition-colors">
                                <td class="px-6 py-4 font-mono">
                                    <div class="font-bold">{{ lote.numero_lote }}</div>
                                    <div class="text-[9px] opacity-60 uppercase">{{ lote.fecha_vencimiento }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-primary text-base">
                                    {{ lote.cantidad_actual }}
                                </td>
                            </tr>
                            <tr v-if="producto.lotes.length === 0">
                                <td colspan="2" class="px-6 py-10 text-center text-muted-foreground italic uppercase text-[10px] tracking-widest">Sin stock registrado</td>
                            </tr>
                        </tbody>
                    </table>
                </AppSectionCard>
            </div>
        </div>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar" 
            :itemName="producto.nombre_comercial" 
            type="producto maestro" 
            @close="mostrarModalEliminar = false" 
            @confirm="confirmarEliminacion" 
        />

    </AppPageShell>
</template>