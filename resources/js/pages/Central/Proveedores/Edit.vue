<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2, Users } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as ProveedorController from '@/actions/App/Http/Controllers/Central/ProveedorController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Proveedores', href: ProveedorController.index.url() },
            { title: 'Editar Proveedor', href: '#' },
        ],
    },
});

interface Proveedor {
    id: number
    razon_social: string
    ruc: string
    contacto: string | null
    telefono: string | null
    email: string | null
    direccion: string | null
    activo: boolean
}

const props = defineProps<{ proveedor: Proveedor }>()

const form = useForm({
    razon_social: props.proveedor.razon_social || '',
    ruc: props.proveedor.ruc || '',
    contacto: props.proveedor.contacto || '',
    telefono: props.proveedor.telefono || '',
    email: props.proveedor.email || '',
    direccion: props.proveedor.direccion || '',
    activo: props.proveedor.activo ?? true,
})


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(ProveedorController.destroy.url(props.proveedor.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

function submit() {
    form.put(ProveedorController.update.url(props.proveedor.id)) 
}


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>
 
<template>
    <AppPageShell :title="'Editar: ' + proveedor.razon_social" variant="narrow">


        <AppPageHeader 
            title="Editar Proveedor" 
            subtitle="Gestión de información legal y contacto comercial."
            :backUrl="ProveedorController.index.url()"
        >
            <template #actions>
                <button
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Dar de Baja
                </button>
            </template>
        </AppPageHeader>


        <AppSectionCard>

            <AppEditContextCard 
                title="Editando Socio Comercial"
                :subtitle="proveedor.razon_social"
                :itemId="proveedor.ruc"
                idLabel="RUC Registrado"
                :icon="Users"
            />

            <form @submit.prevent="submit" class="space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Razón Social <span class="text-destructive">*</span></label>
                        <input
                            v-model="form.razon_social"
                            type="text"
                            placeholder="Nombre legal"
                            :class="inputStyle"
                        />
                        <p v-if="form.errors.razon_social" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.razon_social }}</p>
                    </div>
                    <div>
                        <label :class="labelStyle">Número de RUC <span class="text-destructive">*</span></label>
                        <input
                            v-model="form.ruc"
                            type="text"
                            maxlength="11"
                            placeholder="20XXXXXXXXX"
                            :class="inputStyle"
                        />
                        <p v-if="form.errors.ruc" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.ruc }}</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-muted/10 rounded-xl border border-border/50">
                    <div>
                        <label :class="labelStyle">Persona de Contacto</label>
                        <input
                            v-model="form.contacto"
                            type="text"
                            placeholder="Nombre del representante"
                            :class="inputStyle"
                        />
                    </div>
                    <div>
                        <label :class="labelStyle">Teléfono / Celular</label>
                        <input
                            v-model="form.telefono"
                            type="text"
                            placeholder="Ej: 987654321"
                            :class="inputStyle"
                        />
                    </div>
                </div>


                <div class="space-y-6">
                    <div>
                        <label :class="labelStyle">Correo Electrónico</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="proveedor@empresa.com"
                            :class="inputStyle"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.email }}</p>
                    </div>
    
                    <div>
                        <label :class="labelStyle">Dirección Fiscal / Oficina</label>
                        <textarea
                            v-model="form.direccion"
                            rows="2"
                            placeholder="Calle, número, distrito..."
                            class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all resize-none"
                        />
                    </div>

                    <div class="pt-2 px-1">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input 
                                v-model="form.activo" 
                                type="checkbox" 
                                class="h-5 w-5 rounded border-border bg-background text-primary focus:ring-primary/20"
                            />
                            <span class="text-xs font-bold text-muted-foreground group-hover:text-white transition-colors uppercase tracking-widest">
                                Proveedor habilitado para órdenes
                            </span>
                        </label>
                    </div>
                </div>


                <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                    <Link
                        :href="ProveedorController.index.url()"
                        class="px-6 py-2.5 rounded-lg border border-border text-sm font-bold text-muted-foreground hover:bg-muted transition"
                    >
                        Anular Cambios
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-amber-500 text-white px-10 py-2.5 rounded-lg text-sm font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-widest"
                    >
                        {{ form.processing ? 'Sincronizando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="proveedor.razon_social"
            type="proveedor comercial"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>