<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppFormActions from '@/components/app/AppFormActions.vue'

import * as LaboratorioController from '@/actions/App/Http/Controllers/Central/LaboratorioController'
 

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Laboratorios', href: LaboratorioController.index.url() },
            { title: 'Nuevo', href: '#' },
        ],
    },
});

const form = useForm({
    nombre: '',
    pais: '',
})
 
const submit = () => form.post(LaboratorioController.store.url())


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>
 
<template>

    <AppPageShell title="Nuevo Laboratorio" variant="narrow">


        <AppPageHeader 
            title="Nuevo Laboratorio" 
            subtitle="Registre un nuevo fabricante o marca farmacéutica en el catálogo maestro."
            :backUrl="LaboratorioController.index.url()"
        />


        <AppSectionCard>
            <form @submit.prevent="submit" class="space-y-8">
                
                <div>
                    <label :class="labelStyle">
                        Nombre del Laboratorio <span class="text-destructive">*</span>
                    </label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        placeholder="Ej: Bayer, Pfizer, Roche..."
                        :class="inputStyle"
                    />
                    <p v-if="form.errors.nombre" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.nombre }}</p>
                </div>
 
                <div>
                    <label :class="labelStyle">
                        País de Origen / Sede
                    </label>
                    <input
                        v-model="form.pais"
                        type="text"
                        placeholder="Ej: Alemania, EE.UU., Perú..."
                        :class="inputStyle"
                    />
                    <p v-if="form.errors.pais" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.pais }}</p>
                </div>
 

                <AppFormActions 
                    :backUrl="LaboratorioController.index.url()" 
                    :processing="form.processing" 
                    submitLabel="Registrar Laboratorio"
                />
 
            </form>
        </AppSectionCard>
 
    </AppPageShell>
</template>