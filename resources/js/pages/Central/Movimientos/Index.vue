<script setup lang="ts">
import { router, Link, Head } from '@inertiajs/vue3'
import axios from 'axios'
import { debounce } from 'lodash'
import { Search, Info, X, ArrowUpRight, ArrowDownRight } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
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

interface Paginator {
    data: Movimiento[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{ 
    movimientos: Paginator; 
    filters: { search?: string } 
}>()

const search = ref(props.filters.search ?? '')

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

// Helper para limpiar las flechas de escape HTML de Laravel a texto plano (Evita usar v-html)
const mapearLabelPaginacion = (label: string) => {
    return label
        .replace('&laquo; Previous', '← Anterior')
        .replace('Next &raquo;', 'Siguiente →')
        .replace('&laquo;', '←')
        .replace('&raquo;', '→');
}
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
                                <div class="text-[10px] text-muted-foreground uppercase tracking-tighter mt-1">{{ m.usuario?.name }}</div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="font-bold text-foreground text-base">{{ m.lote?.producto?.nombre_comercial }}</div>
                                <div class="text-[10px] text-muted-foreground font-mono mt-0.5 uppercase">Lote: {{ m.lote?.numero_lote }}</div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center">
                                    <AppStatusBadge :status="m.cantidad > 0 ? 'Enviado' : 'Inactivo'" />
                                    <span class="ml-2 text-[10px] font-bold text-muted-foreground uppercase">{{ m.tipo?.nombre }}</span>
                                </div>
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
                            <td colspan="6" class="py-24 text-center text-muted-foreground italic text-base">No hay movimientos registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-3">
                    <span class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">
                        Total de operaciones: {{ movimientos.total }}
                    </span>
                    <div v-if="movimientos.links?.length > 3" class="flex items-center gap-1">
                        <template v-for="(link, index) in movimientos.links" :key="index">
                            <Link v-if="link.url" :href="link.url"
                                :class="['rounded-lg px-3 py-1.5 text-xs font-bold border transition-all', 
                                link.active ? 'bg-primary border-primary text-primary-foreground shadow-md' : 'border-border bg-background text-muted-foreground hover:bg-muted']"
                            >
                                {{ mapearLabelPaginacion(link.label) }}
                            </Link>
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>
    </AppPageShell>
</template>