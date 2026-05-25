<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, Plus, Edit3, Trash2 } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as CategoriaController from '@/actions/App/Http/Controllers/Central/CategoriaController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Entidades Maestras', href: '#' },
            { title: 'Categorías', href: CategoriaController.index.url() },
        ],
    },
});

interface Categoria {
    id: number
    nombre: string
    descripcion: string | null
}
 
interface Paginator {
    data: Categoria[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}
 
const props = defineProps<{
    categorias: Paginator
    filters: { search?: string }
}>()
 
const search = ref(props.filters.search ?? '')
 

const applyFilters = () => {
    router.get(
        CategoriaController.index.url(),
        { search: search.value },
        { preserveState: true, replace: true }
    )
}
watch(search, debounce(() => applyFilters(), 400))


const mostrarModalEliminar = ref(false)
const categoriaSeleccionada = ref<Categoria | null>(null)

function abrirModalEliminar(c: Categoria) {
    categoriaSeleccionada.value = c
    mostrarModalEliminar.value = true
}

function cerrarModal() {
    mostrarModalEliminar.value = false
    categoriaSeleccionada.value = null
}

function confirmarEliminacion() {
    if (categoriaSeleccionada.value) {
        router.delete(CategoriaController.destroy.url(categoriaSeleccionada.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}
</script>
 
<template>
    <AppPageShell title="Categorías de Productos" variant="full">


        <AppPageHeader 
            title="Categorías" 
            subtitle="Gestión de familias y clasificaciones de productos farmacéuticos."
        >
            <template #actions>
                <Link
                    :href="CategoriaController.create.url()"
                    class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm"
                >
                    <Plus class="size-4" /> Nueva Categoría
                </Link>
            </template>
        </AppPageHeader>


        <AppSectionCard>
            <div class="relative max-w-md">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60">
                    <Search class="size-4" />
                </span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por nombre de categoría..."
                    class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition"
                />
            </div>
        </AppSectionCard>
 

        <AppSectionCard fill noPadding title="Listado de Categorías">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm text-left border-collapse">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border">
                        <tr>
                            <th class="px-8 py-5 w-24">ID</th>
                            <th class="px-8 py-5">Nombre de Categoría</th>
                            <th class="px-8 py-5">Descripción / Notas</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground">
                        <tr v-if="categorias.data.length === 0">
                            <td colspan="4" class="py-24 text-center text-muted-foreground italic text-base">
                                No se encontraron categorías que coincidan con la búsqueda.
                            </td>
                        </tr>
                        <tr
                            v-for="c in categorias.data"
                            :key="c.id"
                            class="hover:bg-muted/10 transition-colors group"
                        >
                            <td class="px-8 py-6 font-mono text-xs text-muted-foreground">#{{ c.id }}</td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-foreground text-base">{{ c.nombre }}</div>
                            </td>
                            <td class="px-8 py-6 text-muted-foreground leading-relaxed max-w-md">
                                {{ c.descripcion ?? '— Sin descripción asignada —' }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-6">
                                    <Link
                                        :href="CategoriaController.edit.url(c.id)"
                                        class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1"
                                    >
                                        <Edit3 class="size-3" /> Editar
                                    </Link>
                                    <button
                                        @click="abrirModalEliminar(c)"
                                        class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1"
                                    >
                                        <Trash2 class="size-3" /> Borrar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
 

            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-[9px] text-muted-foreground font-black uppercase tracking-widest">
                        Total en el sistema: {{ categorias.total }} categorías
                    </span>
                    
                    <div class="flex items-center gap-1">
                        <template v-for="(link, index) in categorias.links" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'rounded-lg px-3 py-1.5 text-xs font-bold border transition-all',
                                    link.active
                                        ? 'bg-primary border-primary text-primary-foreground shadow-md'
                                        : 'border-border bg-background text-muted-foreground hover:bg-muted'
                                ]"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="categoriaSeleccionada?.nombre"
            type="categoría farmacéutica"
            @close="cerrarModal"
            @confirm="confirmarEliminacion"
        />
 
    </AppPageShell>
</template>