<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, Plus, Edit3, Trash2, Building2 } from 'lucide-vue-next'

// --- COMPONENTES ESTRUCTURALES ---
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as LaboratorioController from '@/actions/App/Http/Controllers/Central/LaboratorioController'

// --- CONFIGURACIÓN DEL LAYOUT ---
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Laboratorios', href: LaboratorioController.index.url() },
        ],
    },
});

interface Laboratorio {
    id: number;
    nombre: string;
    pais: string | null;
}
 
interface Paginator {
    data: Laboratorio[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}
 
const props = defineProps<{
    laboratorios: Paginator // Asegúrate que el controlador envíe 'laboratorios'
    filters: { search?: string }
}>()
 
const search = ref(props.filters.search ?? '')
 
// --- LÓGICA DE FILTROS ---
const applyFilters = () => {
    router.get(
        LaboratorioController.index.url(),
        { search: search.value },
        { preserveState: true, replace: true }
    )
}
watch(search, debounce(() => applyFilters(), 400))

// --- LÓGICA DEL MODAL DE ELIMINACIÓN ---
const mostrarModalEliminar = ref(false)
const laboratorioSeleccionado = ref<Laboratorio | null>(null)

function abrirModalEliminar(l: Laboratorio) {
    laboratorioSeleccionado.value = l
    mostrarModalEliminar.value = true
}

function cerrarModal() {
    mostrarModalEliminar.value = false
    laboratorioSeleccionado.value = null
}

function confirmarEliminacion() {
    if (laboratorioSeleccionado.value) {
        router.delete(LaboratorioController.destroy.url(laboratorioSeleccionado.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}
</script>
 
<template>
    <AppPageShell title="Laboratorios" variant="full">
        
        <!-- ENCABEZADO -->
        <AppPageHeader 
            title="Laboratorios" 
            subtitle="Gestión de fabricantes y marcas farmacéuticas."
        >
            <template #actions>
                <Link
                    :href="LaboratorioController.create.url()"
                    class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm"
                >
                    <Plus class="size-4" /> Nuevo Laboratorio
                </Link>
            </template>
        </AppPageHeader>

        <!-- FILTROS -->
        <AppSectionCard>
            <div class="relative max-w-md text-foreground">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60">
                    <Search class="size-4" />
                </span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por nombre o país..."
                    class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-lg border border-border bg-background focus:ring-2 focus:ring-primary/40 outline-none transition"
                />
            </div>
        </AppSectionCard>
 
        <!-- TABLA ESTIRADA -->
        <AppSectionCard fill noPadding title="Registros de Laboratorios">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border">
                        <tr>
                            <th class="px-8 py-5 w-24">ID</th>
                            <th class="px-8 py-5">Nombre del Laboratorio</th>
                            <th class="px-8 py-5">País</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <!-- USAMOS PROPS.LABORATORIOS PARA EVITAR ERRORES DE UNDEFINED -->
                    <tbody v-if="props.laboratorios" class="divide-y divide-border/50 text-foreground">
                        <tr v-for="l in props.laboratorios.data" :key="l.id" class="hover:bg-muted/10 transition-colors group">
                            <td class="px-8 py-6 font-mono text-xs text-muted-foreground">#{{ l.id }}</td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <Building2 class="size-4 text-muted-foreground group-hover:text-primary transition-colors" />
                                    <div class="font-bold text-foreground text-base leading-tight">{{ l.nombre }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-medium">
                                {{ l.pais ?? '—' }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-6">
                                    <Link :href="LaboratorioController.edit.url(l.id)" 
                                          class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline">
                                        Editar
                                    </Link>
                                    <button @click="abrirModalEliminar(l)" 
                                            class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline">
                                        Borrar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.laboratorios.data.length === 0">
                            <td colspan="4" class="py-24 text-center text-muted-foreground italic text-base">
                                No se encontraron laboratorios.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
 
            <!-- PAGINACIÓN CORREGIDA -->
            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <span class="text-[9px] text-muted-foreground font-black uppercase tracking-widest">
                        Total: {{ props.laboratorios.total }} registros
                    </span>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, k) in props.laboratorios.links" :key="k">
                            <Link v-if="link.url" :href="link.url"
                                :class="['rounded-lg px-3 py-1.5 text-xs font-bold border transition-all', link.active ? 'bg-primary border-primary text-primary-foreground shadow-md' : 'border-border bg-background text-foreground hover:bg-muted']">
                                <span v-html="link.label"></span>
                            </Link>
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>

        <!-- MODAL -->
        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="laboratorioSeleccionado?.nombre"
            type="laboratorio"
            @close="cerrarModal"
            @confirm="confirmarEliminacion"
        />
 
    </AppPageShell>
</template>