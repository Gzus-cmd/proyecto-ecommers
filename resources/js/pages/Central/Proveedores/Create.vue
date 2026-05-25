<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppFormActions from '@/components/app/AppFormActions.vue'

import * as ProveedorController from '@/actions/App/Http/Controllers/Central/ProveedorController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Proveedores', href: ProveedorController.index.url() },
            { title: 'Nuevo', href: '#' },
        ],
    },
});

const form = useForm({
    razon_social: '',
    ruc: '',
    contacto: '',
    telefono: '',
    email: '',
    direccion: '',
    activo: true,
})

function submit() {
    form.post(ProveedorController.store.url())
}


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>

<template>

    <AppPageShell title="Nuevo Proveedor" variant="narrow">
        

        <AppPageHeader 
            title="Nuevo Proveedor" 
            subtitle="Registre un nuevo socio comercial para el abastecimiento de la farmacia."
            :backUrl="ProveedorController.index.url()"
        />


        <AppSectionCard>
            <form @submit.prevent="submit" class="space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Razón Social / Nombre Legal <span class="text-destructive">*</span></label>
                        <input
                            v-model="form.razon_social"
                            type="text"
                            placeholder="Ej: Distribuidora Farma SAC"
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
                            placeholder="Ej: 20512345678"
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
                        <label :class="labelStyle">Teléfono de Enlace</label>
                        <input
                            v-model="form.telefono"
                            type="text"
                            placeholder="Ej: 01-4567890"
                            :class="inputStyle"
                        />
                    </div>
                </div>


                <div class="space-y-6">
                    <div>
                        <label :class="labelStyle">Correo Electrónico Corporativo</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="contacto@empresa.com"
                            :class="inputStyle"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.email }}</p>
                    </div>
    
                    <div>
                        <label :class="labelStyle">Dirección Fiscal</label>
                        <textarea
                            v-model="form.direccion"
                            rows="2"
                            placeholder="Ubicación completa del proveedor..."
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
                            <span class="text-xs font-bold text-muted-foreground group-hover:text-foreground transition-colors uppercase tracking-widest">
                                Habilitar proveedor para órdenes de compra
                            </span>
                        </label>
                    </div>
                </div>


                <AppFormActions 
                    :backUrl="ProveedorController.index.url()" 
                    :processing="form.processing" 
                    submitLabel="Registrar Proveedor"
                />
            </form>
        </AppSectionCard>
    </AppPageShell>
</template>