<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, Plus, Edit3, Eye, Trash2 } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as LoteController from '@/actions/App/Http/Controllers/Central/LoteController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión de Inventario', href: '#' },
            { title: 'Lotes y Stock', href: LoteController.index.url() },
        ],
    },
});

interface Lote {
    id: number; numero_lote: string; fecha_vencimiento: string;
    cantidad_inicial: number; cantidad_actual: number; costo_unitario: number;
    estado: string; producto: { nombre_comercial: string; sku: string } | null;
}
interface Paginator { data: Lote[]; total: number; links: any[]; }

const props = defineProps<{ 
    lotes: Paginator, 
    filters: { search?: string; estado?: string },
    estados: string[] 
}>()


const search = ref(props.filters.search ?? '')
const estado = ref(props.filters.estado ?? '')

const applyFilters = () => {
    router.get(LoteController.index.url(), 
    { search: search.value, estado: estado.value }, 
    { preserveState: true, replace: true })
}
watch(search, debounce(() => applyFilters(), 400))
watch(estado, () => applyFilters())


const mostrarModalEliminar = ref(false)
const loteSeleccionado = ref<Lote | null>(null)

const abrirModal = (l: Lote) => { loteSeleccionado.value = l; mostrarModalEliminar.value = true; }
const cerrarModal = () => { mostrarModalEliminar.value = false; loteSeleccionado.value = null; }
const confirmarEliminacion = () => {
    if (loteSeleccionado.value) {
        router.delete(LoteController.destroy.url(loteSeleccionado.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}
</script>

<template>
    <AppPageShell title="Control de Lotes" variant="full">
        

        <AppPageHeader 
            title="Control de Lotes" 
            subtitle="Seguimiento de stock, costos y fechas de vencimiento."
        >
            <template #actions>
                <Link :href="LoteController.create.url()" 
                      class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm">
                    <Plus class="size-4" /> Nuevo Lote
                </Link>
            </template>
        </AppPageHeader>


        <AppSectionCard>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-9 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60"><Search class="size-4" /></span>
                    <input v-model="search" type="text" placeholder="Buscar por lote o producto..." 
                           class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition" />
                </div>
                <div class="md:col-span-3">
                    <select v-model="estado" class="w-full h-11 rounded-xl border-border bg-background/50 px-3 py-2 text-sm text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition cursor-pointer">
                        <option value="">Todos los estados</option>
                        <option v-for="e in estados" :key="e" :value="e">{{ e }}</option>
                    </select>
                </div>
            </div>
        </AppSectionCard>


        <AppSectionCard fill noPadding title="Existencias en Almacén">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm text-left">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border">
                        <tr>
                            <th class="px-6 py-5">Nro. Lote</th>
                            <th class="px-6 py-5">Producto / SKU</th>
                            <th class="px-6 py-5">Vencimiento</th>
                            <th class="px-6 py-4 text-right">Inicial</th>
                            <th class="px-6 py-4 text-right">Actual</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <tr v-for="l in lotes.data" :key="l.id" class="hover:bg-muted/10 transition-colors group">
                            <td class="px-6 py-6 font-mono text-primary font-bold text-xs">
                                {{ l.numero_lote }}
                            </td>
                            <td class="px-6 py-6">
                                <div class="font-bold text-foreground text-base">{{ l.producto?.nombre_comercial ?? '—' }}</div>
                                <div class="text-[10px] text-muted-foreground font-mono uppercase mt-0.5">{{ l.producto?.sku }}</div>
                            </td>
                            <td class="px-6 py-6 text-foreground font-medium">
                                {{ l.fecha_vencimiento }}
                            </td>
                            <td class="px-6 py-6 text-right text-muted-foreground font-medium">
                                {{ l.cantidad_inicial }}
                            </td>
                            <td class="px-6 py-6 text-right">
                                <span class="font-black text-foreground text-lg">{{ l.cantidad_actual }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <AppStatusBadge :status="l.estado" />
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center justify-end gap-5">
                                    <Link :href="LoteController.show.url(l.id)" class="text-primary font-black uppercase text-[10px] tracking-widest hover:underline">Ver</Link>
                                    <Link :href="LoteController.edit.url(l.id)" class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline">Editar</Link>
                                    <button @click="abrirModal(l)" class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="lotes.data.length === 0">
                            <td colspan="7" class="py-20 text-center text-muted-foreground italic text-base">No hay lotes que coincidan con los filtros.</td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">
                        Total en Inventario: {{ lotes.total }} lotes
                    </span>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, index) in lotes.links" :key="index">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                :class="['rounded-lg px-3 py-1.5 text-xs font-bold border transition-all', 
                                link.active ? 'bg-primary border-primary text-primary-foreground shadow-md' : 'border-border bg-background text-muted-foreground hover:bg-muted']" />
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar" 
            :itemName="loteSeleccionado?.numero_lote" 
            type="lote de inventario" 
            @close="cerrarModal" 
            @confirm="confirmarEliminacion" 
        />

    </AppPageShell>
</template>