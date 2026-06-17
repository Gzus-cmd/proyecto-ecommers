<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, UserPlus, UserCog, Trash2, ShieldCheck, MapPin, Mail } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'


import * as UserController from '@/actions/App/Http/Controllers/Central/UserController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Seguridad', href: '#' },
            { title: 'Operadores del Sistema', href: UserController.index.url() },
        ],
    },
});

interface Role {
    id: number;
    name: string;
}

interface Sede {
    id: number;
    nombre: string;
}

interface Usuario {
    id: number;
    name: string;
    email: string;
    sede?: Sede | null;
    roles: Role[];
}
 
interface Paginator {
    data: Usuario[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}
 
const props = defineProps<{
    usuarios: Paginator
    filters: { search?: string }
}>()
 
const search = ref(props.filters.search ?? '')
 
const applyFilters = () => {
    router.get(
        UserController.index.url(),
        { search: search.value },
        { preserveState: true, replace: true }
    )
}

watch(search, debounce(() => applyFilters(), 400))


const mostrarModalEliminar = ref(false)
const usuarioSeleccionado = ref<Usuario | null>(null)

function abrirModalEliminar(u: Usuario) {
    usuarioSeleccionado.value = u
    mostrarModalEliminar.value = true
}

function cerrarModal() {
    mostrarModalEliminar.value = false
    usuarioSeleccionado.value = null
}

function confirmarEliminacion() {
    if (usuarioSeleccionado.value) {
        router.delete(UserController.destroy.url(usuarioSeleccionado.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}

const mapearLabelPaginacion = (label: string) => {
    return label
        .replace('&laquo; Previous', '← Anterior')
        .replace('Next &raquo;', 'Siguiente →')
        .replace('&laquo;', '←')
        .replace('&raquo;', '→');
}
</script>
 
<template>
    <AppPageShell title="Gestión de Operadores" variant="full">

        <AppPageHeader 
            title="Usuarios y Permisos" 
            subtitle="Administración de credenciales, roles de Spatie y asignación de sedes operativas."
        >
            <template #actions>
                <Link
                    :href="UserController.create.url()"
                    class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm"
                >
                    <UserPlus class="size-4" /> Registrar Operador
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
                    placeholder="Buscar por nombre o correo electrónico..."
                    class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition"
                />
            </div>
        </AppSectionCard>
 
        <AppSectionCard fill noPadding title="Lista Blanca de Acceso">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm text-left border-collapse">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border">
                        <tr>
                            <th class="px-8 py-5">Identidad Operativa</th>
                            <th class="px-8 py-5">Nivel de Acceso (Rol)</th>
                            <th class="px-8 py-5">Asignación Geográfica</th>
                            <th class="px-8 py-5 text-right">Configuración</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground">
                        <tr v-if="usuarios.data.length === 0">
                            <td colspan="4" class="py-24 text-center text-muted-foreground italic text-base">
                                No se encontraron operadores registrados en la base de datos.
                            </td>
                        </tr>
                        <tr
                            v-for="u in usuarios.data"
                            :key="u.id"
                            class="hover:bg-muted/10 transition-colors group"
                        >
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-foreground text-base uppercase tracking-tight">{{ u.name }}</span>
                                    <span class="flex items-center gap-1 text-muted-foreground text-[11px] font-mono">
                                        <Mail class="size-3" /> {{ u.email }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <div 
                                        :class="[
                                            'px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5',
                                            u.roles[0]?.name === 'Administrador General' ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : 
                                            u.roles[0]?.name === 'Jefe de Almacén' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 
                                            'bg-slate-500/10 text-slate-400 border border-slate-500/20'
                                        ]"
                                    >
                                        <ShieldCheck class="size-3" />
                                        {{ u.roles[0]?.name || 'Sin Privilegios' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div v-if="u.sede" class="flex items-center gap-2 text-muted-foreground uppercase text-[10px] font-bold">
                                    <MapPin class="size-3 text-primary" />
                                    {{ u.sede.nombre }}
                                </div>
                                <span v-else class="text-[9px] font-black text-muted-foreground/50 uppercase tracking-tighter">Acceso Global / Central</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-6">
                                    <Link
                                        :href="UserController.edit.url(u.id)"
                                        class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1"
                                    >
                                        <UserCog class="size-3" /> Perfil
                                    </Link>
                                    <button
                                        @click="abrirModalEliminar(u)"
                                        class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1"
                                    >
                                        <Trash2 class="size-3" /> Revocar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
 
            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <span class="text-[9px] text-muted-foreground font-black uppercase tracking-widest">
                        Total Operadores: {{ usuarios.total }}
                    </span>
                     
                    <div v-if="usuarios.links.length > 3" class="flex items-center gap-1">
                        <template v-for="(link, index) in usuarios.links" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'rounded-lg px-3 py-1.5 text-xs font-bold border transition-all',
                                    link.active
                                        ? 'bg-primary border-primary text-primary-foreground shadow-md'
                                        : 'border-border bg-background text-muted-foreground hover:bg-muted'
                                ]"
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
            :itemName="usuarioSeleccionado?.name"
            type="operador del sistema"
            @close="cerrarModal"
            @confirm="confirmarEliminacion"
        />
 
    </AppPageShell>
</template>