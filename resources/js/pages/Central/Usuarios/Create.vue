<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ShieldCheck, UserPlus } from 'lucide-vue-next'

import AppFormActions from '@/components/app/AppFormActions.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'

import * as UserController from '@/actions/App/Http/Controllers/Central/UserController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Seguridad', href: '#' },
            { title: 'Operadores', href: UserController.index.url() },
            { title: 'Nuevo Registro', href: '#' },
        ],
    },
});

const props = defineProps<{
    sedes: { id: number, nombre: string }[],
    roles: { id: number, name: string }[]
}>()

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    sede_id: '',
    role: '',
})
 
const submit = () => form.post(UserController.store.url())

const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
const selectStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all appearance-none cursor-pointer";
</script>
 
<template>
    <AppPageShell title="Nuevo Operador" variant="narrow">

        <AppPageHeader 
            title="Registrar Operador" 
            subtitle="Asigne credenciales y privilegios de acceso para el personal técnico."
            :backUrl="UserController.index.url()"
        />

        <AppSectionCard>
            <form @submit.prevent="submit" class="space-y-6">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Nombre Completo <span class="text-destructive">*</span></label>
                        <input v-model="form.name" type="text" placeholder="Ej: Juan Pérez" :class="inputStyle" />
                        <p v-if="form.errors.name" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Correo Institucional <span class="text-destructive">*</span></label>
                        <input v-model="form.email" type="email" placeholder="usuario@pharmavictoria.com" :class="inputStyle" />
                        <p v-if="form.errors.email" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.email }}</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border/40">
                    <div>
                        <label :class="labelStyle">Rol de Sistema (Spatie) <span class="text-destructive">*</span></label>
                        <select v-model="form.role" :class="selectStyle">
                            <option value="" disabled>Seleccione un nivel...</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name.toUpperCase() }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.role }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Sede Asignada (Jurisdicción)</label>
                        <select v-model="form.sede_id" :class="selectStyle">
                            <option value="">ACCESO GLOBAL / CENTRAL</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                                {{ sede.nombre.toUpperCase() }}
                            </option>
                        </select>
                        <p v-if="form.errors.sede_id" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.sede_id }}</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border/40">
                    <div>
                        <label :class="labelStyle">Contraseña Maestra <span class="text-destructive">*</span></label>
                        <input v-model="form.password" type="password" placeholder="••••••••" :class="inputStyle" />
                        <p v-if="form.errors.password" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Confirmar Contraseña <span class="text-destructive">*</span></label>
                        <input v-model="form.password_confirmation" type="password" placeholder="••••••••" :class="inputStyle" />
                    </div>
                </div>

                <div class="pt-6">
                    <AppFormActions 
                        :backUrl="UserController.index.url()" 
                        :processing="form.processing" 
                        submitLabel="Dar de Alta Operador"
                    />
                </div>
            </form>
        </AppSectionCard>
    </AppPageShell>
</template>