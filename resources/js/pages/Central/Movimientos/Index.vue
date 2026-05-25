<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Link, Head } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import axios from 'axios'
import { Search, Info, X, ArrowUpRight, ArrowDownRight } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'


import * as MovimientoInventarioController from '@/actions/App/Http/Controllers/Central/MovimientoInventarioController'
import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión de Inventario', href: '#' },
            { title: 'Kardex (Movimientos)', href: MovimientoInventarioController.index.url() },
        ],
    },
});

interface Movimiento {
    id: number;
    cantidad: number;
    stock_antes: number;
    stock_despues: number;
    fecha_movimiento: string;
    tipo: { nombre: string };
    lote: { numero_lote: string; producto: { nombre_comercial: string } };
    usuario: { name: string };
    movimentable_id: number;
    movimentable_type: string;
}

const props = defineProps<{ movimientos: any, filters: any }>()
const search = ref(props.filters.search)


const modalAbierto = ref(false)
const cargandoModal = ref(false)
const infoTransferencia = ref<any>(null)

const verReferencia = async (mov: Movimiento) => {
    if (mov.movimentable_type.includes('Transferencia')) {
        modalAbierto.value = true
        cargandoModal.value = true
        infoTransferencia.value = null

        try {
            const response = await axios.get(TransferenciaController.show.url(mov.movimentable_id), {
                headers: { 'Accept': 'application/json' }
            })
            infoTransferencia.value = response.data
        } catch (error) {
            console.error("Error al cargar la transferencia", error)
        } finally {
            cargandoModal.value = false
        }
    }
}

watch(search, debounce((value: string) => {
    router.get(MovimientoInventarioController.index.url(), { search: value }, { preserveState: true, replace: true })
}, 300))
</script>

<template>
    <AppPageShell title="Kardex de Inventario" variant="full">
        

        <AppPageHeader 
            title="Kardex de Inventario" 
            subtitle="Historial completo y trazabilidad de movimientos de stock en tiempo real."
        />


        <AppSectionCard>
            <div class="relative max-w-md">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60"><Search class="size-4" /></span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por producto o número de lote..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 transition-all outline-none"
                />
            </div>
        </AppSectionCard>


        <AppSectionCard fill noPadding title="Registro de Movimientos">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-muted/30 text-muted-foreground font-black uppercase text-[9px] tracking-[0.2em]">
                        <tr>
                            <th class="px-6 py-5">Fecha / Usuario</th>
                            <th class="px-6 py-5">Producto / Lote</th>
                            <th class="px-6 py-5">Tipo de Mov.</th>
                            <th class="px-6 py-5 text-center">Variación</th>
                            <th class="px-6 py-5 text-center">Stock Final</th>
                            <th class="px-6 py-5 text-right">Referencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/30">
                        <tr v-for="m in movimientos.data" :key="m.id" class="hover:bg-muted/10 transition-colors group">
                            <td class="px-6 py-6">
                                <div class="font-mono text-xs text-foreground font-bold">{{ m.fecha_movimiento }}</div>
                                <div class="text-[10px] text-muted-foreground uppercase tracking-tighter mt-1">{{ m.usuario.name }}</div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="font-bold text-foreground text-base">{{ m.lote.producto.nombre_comercial }}</div>
                                <div class="text-[10px] text-muted-foreground font-mono mt-0.5 uppercase">Lote: {{ m.lote.numero_lote }}</div>
                            </td>
                            <td class="px-6 py-6">

                                <AppStatusBadge :status="m.cantidad > 0 ? 'Enviado' : 'Inactivo'" />
                                <span class="ml-2 text-[10px] font-bold text-muted-foreground uppercase">{{ m.tipo.nombre }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div :class="['font-black text-lg flex items-center justify-center gap-1', m.cantidad > 0 ? 'text-emerald-500' : 'text-red-500']">
                                    <ArrowUpRight v-if="m.cantidad > 0" class="size-4" />
                                    <ArrowDownRight v-else class="size-4" />
                                    {{ m.cantidad > 0 ? '+' : '' }}{{ m.cantidad }}
                                </div>
                                <div class="text-[9px] text-muted-foreground italic uppercase">Stock previo: {{ m.stock_antes }}</div>
                            </td>
                            <td class="px-6 py-6 text-center font-black">
                                <div class="bg-muted/50 px-4 py-1.5 rounded-lg border border-border/50 inline-block min-w-12.5 text-foreground">
                                    {{ m.stock_despues }}
                                </div>
                            </td>
                            <td class="px-6 py-6 text-right">
                                <button @click="verReferencia(m)" 
                                      class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-primary hover:text-foreground transition-all bg-primary/5 hover:bg-primary/20 px-4 py-2 rounded-lg border border-primary/10 shadow-sm">
                                    <Info class="size-3" /> Ver Detalle
                                </button>
                            </td>
                        </tr>
                        <tr v-if="movimientos.data.length === 0">
                            <td colspan="6" class="py-24 text-center text-muted-foreground italic text-base">No hay movimientos registrados en el periodo actual.</td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">Mostrando registros históricos</span>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, index) in movimientos.links" :key="index">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                :class="['rounded-lg px-3 py-1.5 text-xs font-bold border transition-all', 
                                link.active ? 'bg-primary border-primary text-primary-foreground shadow-md' : 'border-border bg-background text-muted-foreground hover:bg-muted']" />
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>


        <div v-if="modalAbierto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-card w-full max-w-2xl rounded-2xl border border-border/50 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-8 py-5 border-b border-border/50 bg-muted/20 flex justify-between items-center">
                    <h3 class="text-sm font-black uppercase text-foreground tracking-[0.2em]">Detalle del Documento</h3>
                    <button @click="modalAbierto = false" class="text-muted-foreground hover:text-foreground transition-colors p-2 hover:bg-muted rounded-lg"><X class="size-4" /></button>
                </div>

                <div class="p-8 max-h-[70vh] overflow-y-auto">
                    <div v-if="cargandoModal" class="py-12 text-center text-muted-foreground animate-pulse font-black uppercase tracking-widest text-xs">
                        Consultando servidor...
                    </div>

                    <div v-else-if="infoTransferencia" class="space-y-6">
                        <div class="flex justify-between border-b border-border/50 pb-6">
                            <div>
                                <label class="block text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">Tipo / ID</label>
                                <p class="text-2xl font-bold text-foreground">Transferencia #{{ infoTransferencia.id }}</p>
                            </div>
                            <div class="text-right">
                                <label class="block text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">Fecha</label>
                                <p class="text-sm font-mono font-bold text-foreground/80">{{ infoTransferencia.fecha_envio }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">Sede de Destino</label>
                                <p class="text-lg font-bold text-primary">{{ infoTransferencia.sede_destino.nombre }}</p>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase text-muted-foreground tracking-widest mb-1">Estado Operativo</label>
                                <AppStatusBadge :status="infoTransferencia.estado" />
                            </div>
                        </div>

                        <div class="rounded-xl border border-border/50 overflow-hidden bg-muted/5">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-muted/20 text-muted-foreground font-black uppercase text-[8px] tracking-widest border-b border-border/30">
                                    <tr>
                                        <th class="px-6 py-3">Producto / Lote</th>
                                        <th class="px-6 py-3 text-right">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border/20 text-foreground">
                                    <tr v-for="d in infoTransferencia.detalles" :key="d.id">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-sm">{{ d.lote.producto.nombre_comercial }}</div>
                                            <div class="text-muted-foreground font-mono text-[9px]">{{ d.lote.numero_lote }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-base">
                                            {{ d.cantidad }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-border/50 bg-muted/10 flex justify-end">
                    <button @click="modalAbierto = false" class="bg-foreground text-background px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 shadow-md">
                        Cerrar Ficha
                    </button>
                </div>
            </div>
        </div>
    </AppPageShell>
</template>