<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, Plus, Edit3, Trash2, User, Eye } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as ProveedorController from '@/actions/App/Http/Controllers/Central/ProveedorController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Proveedores', href: ProveedorController.index.url() },
        ],
    },
});

interface Proveedor {
    id: number; razon_social: string; ruc: string; contacto: string | null;
    telefono: string | null; email: string | null; activo: boolean;
}
 
interface Paginator {
    data: Proveedor[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}
 
const props = defineProps<{
    proveedores: Paginator
    filters: { search?: string; activo?: string }
}>()
 
const search = ref(props.filters.search ?? '')
const activo = ref(props.filters.activo ?? '')


const applyFilters = () => {
    router.get(
        ProveedorController.index.url(),
        { search: search.value, activo: activo.value },
        { preserveState: true, replace: true }
    )
}

watch(search, debounce(() => applyFilters(), 400))
watch(activo, () => applyFilters())


const mostrarModalEliminar = ref(false)
const proveedorSeleccionado = ref<Proveedor | null>(null)

function abrirModalEliminar(p: Proveedor) {
    proveedorSeleccionado.value = p
    mostrarModalEliminar.value = true
}

function cerrarModal() {
    mostrarModalEliminar.value = false
    proveedorSeleccionado.value = null
}

function confirmarEliminacion() {
    if (proveedorSeleccionado.value) {
        router.delete(ProveedorController.destroy.url(proveedorSeleccionado.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}
</script>
 
<template>
    <AppPageShell title="Gestión de Proveedores" variant="full">
        

        <AppPageHeader 
            title="Proveedores" 
            subtitle="Administración de socios comerciales y fabricantes registrados."
        >
            <template #actions>
                <Link
                    :href="ProveedorController.create.url()"
                    class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm"
                >
                    <Plus class="size-4" /> Nuevo Proveedor
                </Link>
            </template>
        </AppPageHeader>


        <AppSectionCard>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-9 relative text-foreground">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60">
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por razón social, RUC o contacto..."
                        class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition"
                    />
                </div>
                <div class="md:col-span-3">
                    <select
                        v-model="activo"
                        class="w-full h-11 rounded-xl border-border bg-background/50 px-3 py-2 text-sm text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition cursor-pointer"
                    >
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
        </AppSectionCard>


        <AppSectionCard fill noPadding title="Listado de Proveedores Registrados">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-[0.15em] border-b border-border">
                        <tr>
                            <th class="px-8 py-5">Razón Social</th>
                            <th class="px-8 py-5">Identificación (RUC)</th>
                            <th class="px-8 py-5">Contacto / Comunicación</th>
                            <th class="px-8 py-5 text-center">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground">
                        <tr v-for="p in proveedores.data" :key="p.id" class="hover:bg-muted/10 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-white text-base leading-tight">{{ p.razon_social }}</div>
                                <div class="text-[10px] text-muted-foreground uppercase tracking-tighter mt-1">ID: #{{ p.id }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs font-black bg-primary/10 text-primary px-3 py-1 rounded-lg border border-primary/20 tracking-widest">
                                    {{ p.ruc }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2 font-medium text-foreground">
                                    <User class="size-3 text-muted-foreground" /> {{ p.contacto ?? 'No especificado' }}
                                </div>
                                <div class="text-[11px] text-muted-foreground mt-1 lowercase font-medium">{{ p.email ?? 'Sin correo' }}</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <AppStatusBadge :status="p.activo" />
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end items-center gap-6">
                                    <Link :href="ProveedorController.show.url(p.id)" 
                                          class="text-primary font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Eye class="size-3" /> Ver
                                    </Link>
                                    <Link :href="ProveedorController.edit.url(p.id)" 
                                          class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Edit3 class="size-3" /> Editar
                                    </Link>
                                    <button @click="abrirModalEliminar(p)" 
                                            class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Trash2 class="size-3" /> Borrar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="proveedores.data.length === 0">
                            <td colspan="5" class="py-24 text-center text-muted-foreground italic text-base">
                                No se encontraron proveedores en el catálogo maestro.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
 

            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <span class="text-[9px] text-muted-foreground font-black uppercase tracking-widest">
                        Total de proveedores: {{ proveedores.total }}
                    </span>
                    <div v-if="proveedores.links.length > 3" class="flex justify-center gap-2">
                        <template v-for="(link, k) in proveedores.links" :key="k">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="['px-3 py-1.5 text-xs font-bold rounded-lg border transition-all', 
                                         link.active ? 'bg-primary border-primary text-primary-foreground shadow-md shadow-primary/20' : 'border-border/50 text-muted-foreground hover:bg-muted']"
                            />
                        </template>
                    </div>
                </div>
            </template>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="proveedorSeleccionado?.razon_social"
            type="proveedor comercial"
            @close="cerrarModal"
            @confirm="confirmarEliminacion"
        />
 
    </AppPageShell>
</template>