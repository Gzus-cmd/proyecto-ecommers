<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'


import AppFormActions from '@/components/app/AppFormActions.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'

import * as SedeController from '@/actions/App/Http/Controllers/Central/SedeController'


defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Operaciones y Logística', href: '#' },
            { title: 'Sedes', href: SedeController.index.url() },
            { title: 'Nueva', href: '#' },
        ],
    },
});

const form = useForm({
    codigo: '',
    nombre: '',
    direccion: '',
    telefono: '',
    activo: true,
})

const submit = () => form.post(SedeController.store.url())


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>

<template>

    <AppPageShell title="Nueva Sede Local" variant="narrow">
        

        <AppPageHeader 
            title="Nueva Sede" 
            subtitle="Registre un nuevo punto de venta o almacén dentro de la red PharmaVictoria."
            :backUrl="SedeController.index.url()"
        />


        <AppSectionCard>
            <form @submit.prevent="submit" class="space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Código Identificador <span class="text-destructive">*</span></label>
                        <input 
                            v-model="form.codigo" 
                            type="text" 
                            placeholder="Ej: SEDE-01"
                            :class="inputStyle" 
                        />
                        <p v-if="form.errors.codigo" class="text-xs text-red-500 mt-2 font-medium">{{ form.errors.codigo }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Nombre de Sucursal <span class="text-destructive">*</span></label>
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
                        <label :class="labelStyle">Dirección de Sede</label>
                        <textarea 
                            v-model="form.direccion" 
                            rows="2"
                            placeholder="Dirección física completa..."
                            class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all resize-none"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label :class="labelStyle">Número Telefónico</label>
                            <input 
                                v-model="form.telefono" 
                                type="text" 
                                placeholder="Ej: 987654321"
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
                                <span class="text-xs font-bold text-muted-foreground group-hover:text-foreground transition-colors uppercase tracking-widest">
                                    Habilitar para operaciones
                                </span>
                            </label>
                        </div>
                    </div>
                </div>


                <AppFormActions 
                    :backUrl="SedeController.index.url()" 
                    :processing="form.processing" 
                    submitLabel="Registrar Sede"
                />

            </form>
        </AppSectionCard>
    </AppPageShell>
</template>