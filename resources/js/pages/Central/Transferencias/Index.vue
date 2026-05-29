<script setup lang="ts">
import { router, Link, Head } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { Search, Plus, Eye, Send, Edit3, Trash2 } from 'lucide-vue-next'
import { ref, watch } from 'vue'

import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Operaciones y Logística', href: '#' },
            { title: 'Transferencias', href: TransferenciaController.index.url() },
        ],
    },
});

interface Transferencia {
    id: number;
    fecha_envio: string;
    estado: string;
    sede_destino: { nombre: string } | null;
}

interface Paginator {
    data: Transferencia[];
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{ 
    transferencias: Paginator, 
    filters: { search?: string, estado?: string } 
}>()

const search = ref(props.filters.search ?? '')
const estado = ref(props.filters.estado ?? '')

const opcionesEstado = ['Pendiente', 'Enviado', 'Recibido', 'Cancelado']

const applyFilters = () => {
    router.get(
        TransferenciaController.index.url(),
        { search: search.value, estado: estado.value },
        { preserveState: true, replace: true }
    )
}

watch(search, debounce(() => applyFilters(), 400))
watch(estado, () => applyFilters())

const mostrarModalEliminar = ref(false)
const transferenciaSeleccionada = ref<Transferencia | null>(null)

function abrirModalEliminar(t: Transferencia) {
    transferenciaSeleccionada.value = t
    mostrarModalEliminar.value = true
}

function cerrarModal() {
    mostrarModalEliminar.value = false
    transferenciaSeleccionada.value = null
}

function confirmarEliminacion() {
    if (transferenciaSeleccionada.value) {
        router.delete(TransferenciaController.destroy.url(transferenciaSeleccionada.value.id), {
            onSuccess: () => cerrarModal()
        })
    }
}

const procesarEnvio = (id: number) => {
    if (confirm('¿Confirmar envío? Se descontará el stock de forma permanente.')) {
        router.post(TransferenciaController.enviar.url(id))
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
    <AppPageShell title="Logística de Sedes" variant="full">
        
        <AppPageHeader 
            title="Logística de Sedes" 
            subtitle="Gestión y monitoreo de movimientos de mercadería entre farmacias."
        >
            <template #actions>
                <Link :href="TransferenciaController.create.url()" 
                      class="bg-[#b2e2f2] text-[#003d4d] dark:bg-primary dark:text-primary-foreground px-5 py-2.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition flex items-center gap-2 text-sm">
                    <Plus class="size-4" /> Nueva Transferencia
                </Link>
            </template>
        </AppPageHeader>

        <AppSectionCard>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-9 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground/60">
                        <Search class="size-4" />
                    </span>
                    <input v-model="search" type="text" placeholder="Filtrar por sede de destino..." 
                           class="w-full h-11 pl-10 pr-4 py-2 text-sm rounded-xl border-border bg-background/50 text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition" />
                </div>
                <div class="md:col-span-3">
                    <select v-model="estado" 
                            class="w-full h-11 rounded-xl border-border bg-background/50 px-3 py-2 text-sm text-foreground focus:ring-2 focus:ring-primary/40 outline-none transition cursor-pointer">
                        <option value="">Todos los estados</option>
                        <option v-for="opt in opcionesEstado" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                </div>
            </div>
        </AppSectionCard>

        <AppSectionCard fill noPadding title="Registro de Movimientos Logísticos">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-muted/50 text-muted-foreground font-black uppercase text-[10px] tracking-widest border-b border-border">
                        <tr>
                            <th class="px-8 py-5 w-24">ID</th>
                            <th class="px-8 py-5">Fecha Programada</th>
                            <th class="px-8 py-5">Sede Destino</th>
                            <th class="px-8 py-5 text-center">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground">
                        <tr v-for="t in transferencias.data" :key="t.id" class="hover:bg-muted/10 transition-colors group">
                            <td class="px-8 py-6 font-mono text-xs text-muted-foreground">#{{ t.id }}</td>
                            <td class="px-8 py-6 font-medium text-foreground/80">{{ t.fecha_envio }}</td>
                            <td class="px-8 py-6 font-bold text-base">
                                {{ t.sede_destino?.nombre ?? 'Sede no asignada' }}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <AppStatusBadge :status="t.estado" />
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end items-center gap-5">
                                    <Link :href="TransferenciaController.show.url(t.id)" 
                                          class="text-primary font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Eye class="size-3" /> Ver
                                    </Link>
                                    
                                    <button v-if="t.estado === 'Pendiente'" 
                                            @click="procesarEnvio(t.id)" 
                                            class="bg-emerald-500 text-white px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 shadow-sm transition-all flex items-center gap-1">
                                        <Send class="size-3" /> Enviar
                                    </button>
                                    
                                    <Link v-if="t.estado === 'Pendiente'" 
                                          :href="TransferenciaController.edit.url(t.id)" 
                                          class="text-amber-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Edit3 class="size-3" /> Editar
                                    </Link>

                                    <button v-if="t.estado === 'Pendiente'" 
                                            @click="abrirModalEliminar(t)" 
                                            class="text-red-500 font-black uppercase text-[10px] tracking-widest hover:underline flex items-center gap-1">
                                        <Trash2 class="size-3" /> Anular
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="transferencias.data.length === 0">
                            <td colspan="5" class="py-24 text-center text-muted-foreground italic text-base">
                                No se encontraron transferencias con los parámetros seleccionados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <template #footer>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4">
                    <span class="text-[9px] text-muted-foreground uppercase font-black tracking-widest">
                        Registros en este módulo: {{ transferencias.total }}
                    </span>
                    <div v-if="transferencias.links.length > 3" class="flex justify-center gap-2">
                        <template v-for="(link, k) in transferencias.links" :key="k">
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
            :itemName="`Transferencia #${transferenciaSeleccionada?.id} a ${transferenciaSeleccionada?.sede_destino?.nombre ?? 'Sede'}`"
            type="transferencia de mercadería"
            @close="cerrarModal"
            @confirm="confirmarEliminacion"
        />
    </AppPageShell>
</template>