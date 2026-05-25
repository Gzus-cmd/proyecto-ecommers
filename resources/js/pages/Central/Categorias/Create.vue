<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppFormActions from '@/components/app/AppFormActions.vue'

import * as CategoriaController from '@/actions/App/Http/Controllers/Central/CategoriaController'
 

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Entidades Maestras', href: '#' },
            { title: 'Categorías', href: CategoriaController.index.url() },
            { title: 'Nueva', href: '#' },
        ],
    },
});

const form = useForm({
    nombre: '',
    descripcion: '',
})
 
const submit = () => form.post(CategoriaController.store.url())


const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
</script>
 
<template>

    <AppPageShell title="Nueva Categoría" variant="narrow">


        <AppPageHeader 
            title="Nueva Categoría" 
            subtitle="Defina una nueva familia para la clasificación y organización del catálogo farmacéutico."
            :backUrl="CategoriaController.index.url()"
        />


        <AppSectionCard>
            <form @submit.prevent="submit" class="space-y-8">
                
                <div>
                    <label :class="labelStyle">
                        Nombre de la Categoría <span class="text-destructive">*</span>
                    </label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        placeholder="Ej: Analgésicos, Antibióticos..."
                        :class="inputStyle"
                    />
                    <p v-if="form.errors.nombre" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.nombre }}</p>
                </div>
 
                <div>
                    <label :class="labelStyle">Descripción General</label>
                    <textarea
                        v-model="form.descripcion"
                        rows="4"
                        placeholder="Describa brevemente el uso o características de esta categoría..."
                        class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all resize-none"
                    />
                    <p v-if="form.errors.descripcion" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.descripcion }}</p>
                </div>
 

                <AppFormActions 
                    :backUrl="CategoriaController.index.url()" 
                    :processing="form.processing" 
                    submitLabel="Registrar Categoría"
                />
 
            </form>
        </AppSectionCard>
 
    </AppPageShell>
</template>