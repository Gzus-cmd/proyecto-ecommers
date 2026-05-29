<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppFormActions from '@/components/app/AppFormActions.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import * as LoteController from '@/actions/App/Http/Controllers/Central/LoteController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Inventario', href: '#' },
            { title: 'Lotes', href: LoteController.index.url() },
            { title: 'Nuevo', href: '#' },
        ],
    },
});

defineProps<{ productos: any[], estados: string[] }>()

const form = useForm({
    producto_id: null, numero_lote: '', fecha_fabricacion: '',
    fecha_ingreso: new Date().toISOString().split('T')[0],
    fecha_vencimiento: '', cantidad_inicial: 1, cantidad_actual: 1,
    costo_unitario: 0, estado: 'Disponible',
})

const submit = () => form.post(LoteController.store.url())

const labelStyle = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2";
const inputStyle = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all";
</script>

<template>
    <AppPageShell title="Nuevo Lote" variant="narrow">
        
        <AppPageHeader 
            title="Nuevo Lote" 
            subtitle="Ingrese la información de ingreso y caducidad de la mercadería."
            :backUrl="LoteController.index.url()"
        />

        <AppSectionCard>
            <form @submit.prevent="submit" class="space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label :class="labelStyle">Producto <span class="text-destructive">*</span></label>
                        <select v-model="form.producto_id" :class="inputStyle">
                            <option :value="null">Seleccionar producto...</option>
                            <option v-for="p in productos" :key="p.id" :value="p.id">[{{ p.sku }}] {{ p.nombre_comercial }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label :class="labelStyle">Nro. Lote</label>
                            <input v-model="form.numero_lote" type="text" :class="inputStyle" />
                        </div>
                        <div>
                            <label :class="labelStyle">Estado</label>
                            <select v-model="form.estado" :class="inputStyle">
                                <option v-for="e in estados" :key="e" :value="e">{{ e }}</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-muted/10 rounded-xl border border-border/50">
                    <div>
                        <label :class="labelStyle">Fabricación</label>
                        <input v-model="form.fecha_fabricacion" type="date" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Ingreso</label>
                        <input v-model="form.fecha_ingreso" type="date" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Vencimiento</label>
                        <input v-model="form.fecha_vencimiento" type="date" :class="inputStyle" />
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label :class="labelStyle">Cant. Inicial</label>
                        <input v-model.number="form.cantidad_inicial" type="number" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Cant. Actual</label>
                        <input v-model.number="form.cantidad_actual" type="number" :class="inputStyle" />
                    </div>
                    <div>
                        <label :class="labelStyle">Costo Unit. (S/)</label>
                        <input v-model.number="form.costo_unitario" type="number" step="0.01" :class="inputStyle" />
                    </div>
                </div>

                <AppFormActions 
                    :backUrl="LoteController.index.url()" 
                    :processing="form.processing" 
                    submitLabel="Registrar Lote"
                />
            </form>
        </AppSectionCard>

    </AppPageShell>
</template>