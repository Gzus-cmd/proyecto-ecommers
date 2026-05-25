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

import * as ProductoMaestroController from '@/actions/App/Http/Controllers/Central/ProductoMaestroController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión de Inventario', href: '#' },
            { title: 'Productos Maestro', href: ProductoMaestroController.index.url() },
        ],
    },
});

interface Producto {
    id: number; sku: string; nombre_comercial: string; nombre_generico: string | null;
    categoria: { nombre: string } | null; requiere_receta: boolean; activo: boolean;
}
interface Paginator { data: Producto[]; total: number; links: any[]; }

const props = defineProps<{ productos: Paginator, filters: { search?: string; activo?: string } }>()

const search = ref(props.filters.search ?? '')
const activo = ref(props.filters.activo ?? '')

const applyFilters = () => {
    router.get(ProductoMaestroController.index.url(), 
    { search: search.value, activo: activo.value }, 
    { preserveState: true, replace: true })
}
watch(search, debounce(() => applyFilters(), 400))
watch(activo, () => applyFilters())

const mostrarModalEliminar = ref(false)
const productoSeleccionado = ref<Producto | null>(null)

const abrirModal = (p: Producto) => { productoSeleccionado.value = p; mostrarModalEliminar.value = true; }
const confirmarEliminacion = () => {
    if (productoSeleccionado.value) {
        router.delete(ProductoMaestroController.destroy.url(productoSeleccionado.value.id), {
            onSuccess: () => { mostrarModalEliminar.value = false; }
        })
    }
}
</script>

<template>
    <AppPageShell title="Productos Maestro" variant="full">
        
        <AppPageHeader title="Productos Maestro" subtitle="Catálogo central de farmacia.">
            <template #actions>
                <Link :href="ProductoMaestroController.create.url()" 
                      class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm">
                    <Plus class="size-4" /> Nuevo Producto
                </Link>
            </template>
        </AppPageHeader>

        <AppSectionCard>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-9 relative text-foreground">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60"><Search class="size-4" /></span>
                    <input v-model="search" type="text" placeholder="Buscar por SKU o nombre..." 
                           class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition" />
                </div>
                <div class="md:col-span-3">
                    <select v-model="activo" class="w-full h-11 rounded-xl border-border bg-background px-3 py-2 text-sm text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition cursor-pointer">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
        </AppSectionCard>

        <AppSectionCard fill noPadding>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm text-left">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[9px] tracking-widest border-b border-border">
                        <tr>
                            <th class="px-8 py-5">SKU / Producto</th>
                            <th class="px-8 py-5">Categoría</th>
                            <th class="px-8 py-5 text-center">Receta</th>
                            <th class="px-8 py-5 text-center">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground">
                        <tr v-for="p in productos.data" :key="p.id" class="hover:bg-muted/20 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-base leading-tight">{{ p.nombre_comercial }}</div>
                                <div class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter mt-1">{{ p.sku }}</div>
                            </td>
                            <td class="px-8 py-6 font-medium">{{ p.categoria?.nombre ?? '—' }}</td>
                            <td class="px-8 py-6 text-center">
                                <AppStatusBadge :status="p.requiere_receta ? 'Sí' : 'No'" />
                            </td>
                            <td class="px-8 py-6 text-center">
                                <AppStatusBadge :status="p.activo" />
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-6">
                                    <Link :href="ProductoMaestroController.show.url(p.id)" 
                                          class="text-primary font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        Ver
                                    </Link>
                                    <Link :href="ProductoMaestroController.edit.url(p.id)" 
                                          class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        Editar
                                    </Link>
                                    <button @click="abrirModal(p)" 
                                            class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        Borrar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <span class="text-[9px] text-muted-foreground uppercase font-black tracking-widest">Registros Totales: {{ productos.total }}</span>

                </div>
            </template>
        </AppSectionCard>

        <DeleteConfirmModal :show="mostrarModalEliminar" :itemName="productoSeleccionado?.nombre_comercial" type="producto" @close="mostrarModalEliminar = false" @confirm="confirmarEliminacion" />
    </AppPageShell>
</template>