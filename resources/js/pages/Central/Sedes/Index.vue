<script setup lang="ts">
import { router, Head, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, Plus, Edit3, Trash2, MapPin } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as SedeController from '@/actions/App/Http/Controllers/Central/SedeController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Operaciones y Logística', href: '#' },
            { title: 'Sedes', href: SedeController.index.url() },
        ],
    },
});

interface Sede {
    id: number; codigo: string; nombre: string; direccion: string | null;
    telefono: string | null; activo: boolean;
}

interface Paginator {
    data: Sede[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    sedes: Paginator
    filters: { search?: string, activo?: string }
}>()

const search = ref(props.filters.search ?? '')
const activo = ref(props.filters.activo ?? '')

const applyFilters = () => {
    router.get(
        SedeController.index.url(),
        { search: search.value, activo: activo.value },
        { preserveState: true, replace: true }
    )
}
watch(search, debounce(() => applyFilters(), 400))
watch(activo, () => applyFilters())

const mostrarModalEliminar = ref(false)
const sedeSeleccionada = ref<Sede | null>(null)

function abrirModalEliminar(s: Sede) {
    sedeSeleccionada.value = s
    mostrarModalEliminar.value = true
}

function cerrarModal() {
    mostrarModalEliminar.value = false
    sedeSeleccionada.value = null
}

function confirmarEliminacion() {
    if (sedeSeleccionada.value) {
        router.delete(SedeController.destroy.url(sedeSeleccionada.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}

// Helper para mapear las flechas de escape HTML de Laravel a texto plano (Evita usar v-html)
const mapearLabelPaginacion = (label: string) => {
    return label
        .replace('&laquo; Previous', '← Anterior')
        .replace('Next &raquo;', 'Siguiente →')
        .replace('&laquo;', '←')
        .replace('&raquo;', '→');
}
</script>

<template>
    <AppPageShell title="Sedes y Sucursales" variant="full">
        
        <AppPageHeader 
            title="Sedes" 
            subtitle="Administración de locales y sucursales de la cadena."
        >
            <template #actions>
                <Link
                    :href="SedeController.create.url()"
                    class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm"
                >
                    <Plus class="size-4" /> Nueva Sede
                </Link>
            </template>
        </AppPageHeader>

        <AppSectionCard>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-9 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60">
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre o código..."
                        class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition"
                    />
                </div>
                <div class="md:col-span-3">
                    <select
                        v-model="activo"
                        class="w-full h-11 rounded-xl border-border bg-background/50 px-3 py-2 text-sm text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition cursor-pointer"
                    >
                        <option value="">Todos los estados</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
            </div>
        </AppSectionCard>

        <AppSectionCard fill noPadding title="Listado de Sucursales">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-[0.15em] border-b border-border">
                        <tr>
                            <th class="px-8 py-5 w-24">ID</th>
                            <th class="px-8 py-5">Código</th>
                            <th class="px-8 py-5">Nombre / Ubicación</th>
                            <th class="px-8 py-5 text-center">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground">
                        <tr v-for="s in sedes.data" :key="s.id" class="hover:bg-muted/10 transition-colors group">
                            <td class="px-8 py-6 font-mono text-xs text-muted-foreground">#{{ s.id }}</td>
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs font-black bg-primary/10 text-primary px-3 py-1 rounded-lg border border-primary/20 tracking-widest">
                                    {{ s.codigo }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-base leading-tight">{{ s.nombre }}</div>
                                <div class="text-[10px] text-muted-foreground uppercase tracking-widest mt-1 flex items-center gap-1">
                                    <MapPin class="size-2.5" /> {{ s.direccion ?? 'Sin dirección registrada' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <AppStatusBadge :status="s.activo" />
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-6">
                                    <Link :href="SedeController.edit.url(s.id)" 
                                          class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Edit3 class="size-3" /> Editar
                                    </Link>
                                    <button @click="abrirModalEliminar(s)" 
                                            class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Trash2 class="size-3" /> Borrar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="sedes.data.length === 0">
                            <td colspan="5" class="py-24 text-center text-muted-foreground italic text-base">
                                No se encontraron sedes o sucursales registradas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <span class="text-[9px] text-muted-foreground font-black uppercase tracking-widest">
                        Sedes registradas: {{ sedes.total }}
                    </span>
                    <div v-if="sedes.links.length > 3" class="flex justify-center gap-2">
                        <template v-for="(link, k) in sedes.links" :key="k">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="['px-3 py-1.5 text-xs font-bold rounded-lg border transition-all', 
                                         link.active ? 'bg-primary border-primary text-primary-foreground shadow-md shadow-primary/20' : 'border-border/50 text-muted-foreground hover:bg-muted']"
                            >
                                {{ mapearLabelPaginacion(link.label) }}
                            </Link>
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>

        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="sedeSeleccionada?.nombre"
            type="sede local"
            @close="cerrarModal"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>