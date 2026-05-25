<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppFormActions from '@/components/app/AppFormActions.vue'
import ProductoForm from './ProductoForm.vue'
import * as ProductoMaestroController from '@/actions/App/Http/Controllers/Central/ProductoMaestroController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Inventario', href: '#' },
            { title: 'Productos', href: ProductoMaestroController.index.url() },
            { title: 'Nuevo', href: '#' },
        ],
    },
});

defineProps<{ categorias: any[], laboratorios: any[], proveedores: any[] }>()

const form = useForm({
    sku: '', nombre_comercial: '', nombre_generico: '', descripcion: '',
    categoria_id: null, laboratorio_id: null, proveedor_id: null,
    requiere_receta: false, registro_sanitario: '', concentracion: '',
    forma_farmaceutica: '', unidad_medida: '', stock_minimo: 0, activo: true,
})

const submit = () => form.post(ProductoMaestroController.store.url())
</script>

<template>
    <AppPageShell title="Nuevo Producto" variant="narrow">
        
        <AppPageHeader 
            title="Nuevo Producto" 
            subtitle="Registre las especificaciones técnicas y legales del nuevo fármaco."
            :backUrl="ProductoMaestroController.index.url()"
        />

        <AppSectionCard>
            <ProductoForm :form="form" :categorias="categorias" :laboratorios="laboratorios" :proveedores="proveedores" @submit="submit">
                <template #actions>
                    <AppFormActions 
                        :backUrl="ProductoMaestroController.index.url()" 
                        :processing="form.processing" 
                        submitLabel="Registrar Producto"
                    />
                </template>
            </ProductoForm>
        </AppSectionCard>

    </AppPageShell>
</template>