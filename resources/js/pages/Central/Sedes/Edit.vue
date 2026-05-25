<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2, MapPin } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as SedeController from '@/actions/App/Http/Controllers/Central/SedeController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Operaciones y Logística', href: '#' },
            { title: 'Sedes', href: SedeController.index.url() },
            { title: 'Editar Sede', href: '#' },
        ],
    },
});

interface Sede {
    id: number; codigo: string; nombre: string; direccion: string | null;
    telefono: string | null; activo: boolean;
}

const props = defineProps<{ sede: Sede }>()

const form = useForm({
    codigo: props.sede.codigo,
    nombre: props.sede.nombre,
    direccion: props.sede.direccion ?? '',
    telefono: props.sede.telefono ?? '',
    activo: props.sede.activo,
})


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(SedeController.destroy.url(props.sede.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

const submit = () => {
    form.put(SedeController.update.url(props.sede.id))
}


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>

<template>
    <AppPageShell :title="'Editar Sede: ' + sede.nombre" variant="narrow">
        

        <AppPageHeader 
            title="Editar Sede" 
            subtitle="Gestione los datos de contacto y ubicación de la sucursal."
            :backUrl="SedeController.index.url()"
        >
            <template #actions>
                <button
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Eliminar Sede
                </button>
            </template>
        </AppPageHeader>


        <AppSectionCard>
            <AppEditContextCard 
                title="Editando Sede Local"
                :subtitle="sede.nombre"
                :itemId="sede.codigo"
                idLabel="Código de Sede"
                :icon="MapPin"
            />

            <form @submit.prevent="submit" class="space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Código de Sucursal <span class="text-destructive">*</span></label>
                        <input 
                            v-model="form.codigo" 
                            type="text" 
                            placeholder="Ej: SEDE-01"
                            :class="inputStyle" 
                        />
                        <p v-if="form.errors.codigo" class="text-xs text-red-500 mt-2 font-medium">{{ form.errors.codigo }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Nombre Comercial <span class="text-destructive">*</span></label>
                        <input 
                            v-model="form.nombre" 
                            type="text" 
                            placeholder="Ej: Farmacia Central"
                            :class="inputStyle" 
                        />
                        <p v-if="form.errors.nombre" class="text-xs text-red-500 mt-2 font-medium">{{ form.errors.nombre }}</p>
                    </div>
                </div>


                <div class="space-y-6">
                    <div>
                        <label :class="labelStyle">Dirección Física Completa</label>
                        <textarea 
                            v-model="form.direccion" 
                            rows="2"
                            placeholder="Calle, Número, Distrito..."
                            class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all resize-none"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label :class="labelStyle">Teléfono de Enlace</label>
                            <input 
                                v-model="form.telefono" 
                                type="text" 
                                placeholder="Ej: (01) 234 5678"
                                :class="inputStyle" 
                            />
                        </div>

                        <div class="pb-3 px-1">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input 
                                    v-model="form.activo" 
                                    type="checkbox" 
                                    class="h-5 w-5 rounded border-border bg-background text-primary focus:ring-primary/20"
                                />
                                <span class="text-xs font-bold text-muted-foreground group-hover:text-white transition-colors uppercase tracking-widest">
                                    Sede operativa / activa
                                </span>
                            </label>
                        </div>
                    </div>
                </div>


                <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                    <Link 
                        :href="SedeController.index.url()" 
                        class="px-8 py-3 rounded-xl border border-border font-bold text-muted-foreground hover:bg-muted transition-all text-xs uppercase tracking-widest"
                    >
                        Cancelar
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="bg-amber-500 text-white px-10 py-3 rounded-xl font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-widest text-xs"
                    >
                        {{ form.processing ? 'Guardando...' : 'Actualizar Sede' }}
                    </button>
                </div>
            </form>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="sede.nombre"
            type="sede local"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>