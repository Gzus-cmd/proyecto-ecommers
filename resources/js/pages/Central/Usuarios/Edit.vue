<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { ShieldCheck, UserCog, Trash2, Key } from 'lucide-vue-next'
import { ref } from 'vue'

import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

import * as UserController from '@/actions/App/Http/Controllers/Central/UserController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Seguridad', href: '#' },
            { title: 'Operadores', href: UserController.index.url() },
            { title: 'Editar Perfil', href: '#' },
        ],
    },
});

interface Usuario {
    id: number;
    name: string;
    email: string;
    sede_id: number | null;
    roles: { name: string }[];
}
 
const props = defineProps<{ 
    usuario: Usuario,
    sedes: { id: number, nombre: string }[],
    roles: { id: number, name: string }[]
}>()
 
const form = useForm({
    name: props.usuario.name,
    email: props.usuario.email,
    sede_id: props.usuario.sede_id ?? '',
    role: props.usuario.roles[0]?.name ?? '',
    password: '',
    password_confirmation: '',
})

const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(UserController.destroy.url(props.usuario.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

const submit = () => form.put(UserController.update.url(props.usuario.id))

const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40";
const selectStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all appearance-none cursor-pointer";
</script>
 
<template>
    <AppPageShell :title="'Perfil: ' + usuario.name" variant="narrow">

        <AppPageHeader title="Editar Operador" :backUrl="UserController.index.url()">
            <template #actions>
                <button
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Revocar Acceso
                </button>
            </template>
        </AppPageHeader>

        <AppSectionCard>
            <AppEditContextCard 
                title="Configuración de Credenciales"
                :subtitle="usuario.name"
                :itemId="usuario.id"
                idLabel="Registro de Operador"
                :icon="ShieldCheck"
            />

            <form @submit.prevent="submit" class="space-y-6">
                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Nombre Completo</label>
                        <input v-model="form.name" type="text" :class="inputStyle" />
                        <p v-if="form.errors.name" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Correo Institucional</label>
                        <input v-model="form.email" type="email" :class="inputStyle" />
                        <p v-if="form.errors.email" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.email }}</p>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border/40">
                    <div>
                        <label :class="labelStyle">Nivel de Acceso (Rol)</label>
                        <select v-model="form.role" :class="selectStyle">
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name.toUpperCase() }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.role }}</p>
                    </div>

                    <div>
                        <label :class="labelStyle">Sede Operativa</label>
                        <select v-model="form.sede_id" :class="selectStyle">
                            <option value="">ACCESO GLOBAL</option>
                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">
                                {{ sede.nombre.toUpperCase() }}
                            </option>
                        </select>
                    </div>
                </div>


                <div class="pt-4 border-t border-border/40">
                    <div class="flex items-center gap-2 mb-4">
                        <Key class="size-3 text-amber-500" />
                        <span class="text-[9px] font-black uppercase tracking-widest text-amber-500">Actualizar Contraseña (Solo si desea cambiarla)</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label :class="labelStyle">Nueva Contraseña</label>
                            <input v-model="form.password" type="password" placeholder="••••••••" :class="inputStyle" />
                            <p v-if="form.errors.password" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label :class="labelStyle">Confirmar Nueva Contraseña</label>
                            <input v-model="form.password_confirmation" type="password" placeholder="••••••••" :class="inputStyle" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                    <Link :href="UserController.index.url()" class="px-6 py-2.5 rounded-lg border border-border text-sm font-bold text-muted-foreground hover:bg-muted transition">
                        Cancelar
                    </Link>
                    <button type="submit" :disabled="form.processing" class="bg-amber-500 text-white px-10 py-2.5 rounded-lg text-sm font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-widest">
                        {{ form.processing ? 'Actualizando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>
        </AppSectionCard>

        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="usuario.name"
            type="operador del sistema"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />
    </AppPageShell>
</template>