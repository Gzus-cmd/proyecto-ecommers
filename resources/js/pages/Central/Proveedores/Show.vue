<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ArrowLeft, Edit3, Trash2, Mail, Phone, User, MapPin, Fingerprint } from 'lucide-vue-next'


import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppDetailItem from '@/components/app/AppDetailItem.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'
import AppWatermark from '@/components/app/AppWatermark.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue' 

import * as ProveedorController from '@/actions/App/Http/Controllers/Central/ProveedorController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Abastecimiento', href: '#' },
            { title: 'Proveedores', href: ProveedorController.index.url() },
            { title: 'Ficha de Proveedor', href: '#' },
        ],
    },
});

interface Proveedor {
    id: number; razon_social: string; ruc: string; contacto: string | null;
    telefono: string | null; email: string | null; direccion: string | null;
    activo: boolean; created_at: string | null; updated_at: string | null;
}

const props = defineProps<{ proveedor: Proveedor }>()

const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(ProveedorController.destroy.url(props.proveedor.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

const labelStyle = "block text-[9px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-1";
</script>

<template>
    <Head :title="proveedor.razon_social" />

    <AppPageShell :title="proveedor.razon_social" variant="wide" class="max-w-7xl">
        

        <AppPageHeader 
            title="Ficha de Proveedor" 
            :backUrl="ProveedorController.index.url()"
        >
            <template #actions>
                <Link :href="ProveedorController.edit.url(props.proveedor.id)" 
                      class="bg-primary/10 text-primary border border-primary/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <Edit3 class="size-3.5" /> Editar Datos
                </Link>
                <button @click="mostrarModalEliminar = true" 
                        class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all flex items-center gap-2 shadow-sm">
                    <Trash2 class="size-3.5" /> Dar de Baja
                </button>
            </template>
        </AppPageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            

            <div class="lg:col-span-8 space-y-6">
                

                <AppSectionCard class="relative min-h-70 flex flex-col justify-center">

                    <AppWatermark>
                        <AppLogoIcon class="size-72 fill-current text-foreground opacity-100" />
                    </AppWatermark>
                    
                    <div class="relative z-10">
                        <div class="mb-6">
                            <AppStatusBadge :status="proveedor.activo" />
                        </div>

                        <div class="mb-12">
                            <h1 class="text-4xl font-black text-foreground leading-tight uppercase tracking-tighter">
                                {{ proveedor.razon_social }}
                            </h1>
                            <div class="flex items-center gap-2 text-primary mt-2">
                                <Fingerprint class="size-4" />
                                <p class="text-xs font-bold uppercase tracking-widest">Socio Comercial Registrado</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-border/50 pt-8">
                            <AppDetailItem label="Número de RUC (Identificación Fiscal)" :value="proveedor.ruc" mono highlight class="text-2xl" />
                            <AppDetailItem label="ID de Sistema" :value="'PRV-' + proveedor.id.toString().padStart(5, '0')" mono class="text-xl" />
                        </div>
                    </div>
                </AppSectionCard>


                <AppSectionCard title="Información de Contacto Directo">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-start gap-4 p-5 bg-muted/10 rounded-2xl border border-border/50">
                            <div class="p-3 bg-primary/10 rounded-xl text-primary"><User class="size-5" /></div>
                            <AppDetailItem label="Representante / Contacto" :value="proveedor.contacto || 'No asignado'" class="text-lg" />
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-muted/10 rounded-2xl border border-border/50">
                            <div class="p-3 bg-primary/10 rounded-xl text-primary"><Phone class="size-5" /></div>
                            <AppDetailItem label="Teléfono de Enlace" :value="proveedor.telefono || 'Sin teléfono'" class="text-lg" />
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-muted/10 rounded-2xl border border-border/50 md:col-span-2">
                            <div class="p-3 bg-primary/10 rounded-xl text-primary"><Mail class="size-5" /></div>
                            <AppDetailItem label="Correo Electrónico Corporativo">
                                <a v-if="proveedor.email" :href="`mailto:${proveedor.email}`" class="text-primary font-bold text-lg hover:underline break-all">
                                    {{ proveedor.email }}
                                </a>
                                <span v-else class="text-muted-foreground italic">No registrado</span>
                            </AppDetailItem>
                        </div>
                    </div>
                </AppSectionCard>
            </div>


            <div class="lg:col-span-4 space-y-6">
                

                <AppSectionCard title="Ubicación Fiscal">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-primary/10 rounded-xl text-primary"><MapPin class="size-5" /></div>
                        <AppDetailItem label="Dirección de Oficina / Planta" :value="proveedor.direccion || 'Dirección no registrada'" class="text-base italic leading-relaxed" />
                    </div>
                </AppSectionCard>


                <AppSectionCard noPadding title="Trazabilidad">
                    <div class="p-8 space-y-6">
                        <div class="flex justify-between items-center border-b border-border/30 pb-4">
                            <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Fecha de Registro</span>
                            <span class="text-xs font-mono text-foreground/70 font-bold">{{ proveedor.created_at || '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Última Edición</span>
                            <span class="text-xs font-mono text-foreground/70 font-bold">{{ proveedor.updated_at || '—' }}</span>
                        </div>
                    </div>
                </AppSectionCard>
            </div>
        </div>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar" 
            :itemName="proveedor.razon_social" 
            type="proveedor comercial" 
            @close="mostrarModalEliminar = false" 
            @confirm="confirmarEliminacion" 
        />

    </AppPageShell>
</template>