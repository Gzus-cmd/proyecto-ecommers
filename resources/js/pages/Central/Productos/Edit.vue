<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2 } from 'lucide-vue-next'
import { ref } from 'vue'

import AppEditContextCard from '@/components/app/AppEditContextCard.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'
import ProductoForm from './ProductoForm.vue'

import * as ProductoMaestroController from '@/actions/App/Http/Controllers/Central/ProductoMaestroController'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestión', href: '#' },
            { title: 'Productos', href: ProductoMaestroController.index.url() },
            { title: 'Editar', href: '#' }
        ]
    }
});

const props = defineProps<{
    producto: any;
    categorias: any[];
    laboratorios: any[];
    proveedores: any[];
}>()

const form = useForm({
    sku: props.producto.sku,
    nombre_comercial: props.producto.nombre_comercial,
    nombre_generico: props.producto.nombre_generico ?? '',
    descripcion: props.producto.descripcion ?? '',
    categoria_id: props.producto.categoria_id,
    laboratorio_id: props.producto.laboratorio_id,
    proveedor_id: props.producto.proveedor_id,
    requiere_receta: props.producto.requiere_receta,
    registro_sanitario: props.producto.registro_sanitario ?? '',
    concentracion: props.producto.concentracion ?? '',
    forma_farmaceutica: props.producto.forma_farmaceutica ?? '',
    unidad_medida: props.producto.unidad_medida ?? '',
    stock_minimo: props.producto.stock_minimo,
    activo: props.producto.activo,
})

const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(ProductoMaestroController.destroy.url(props.producto.id))
}
const submit = () => {
    form.put(ProductoMaestroController.update.url(props.producto.id))
}
</script>

<template>
    <AppPageShell :title="'Editar: ' + producto.nombre_comercial" variant="narrow">
        
        <AppPageHeader title="Editar Producto" :backUrl="ProductoMaestroController.index.url()">
            <template #actions>
                <button type="button" @click="mostrarModalEliminar = true" class="bg-red-500/10 text-red-500 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                    <Trash2 class="size-4 inline mr-1" /> Eliminar Producto
                </button>
            </template>
        </AppPageHeader>

        <AppSectionCard>
            <AppEditContextCard 
                title="Actualización de Ficha Técnica"
                :subtitle="producto.nombre_comercial"
                :itemId="producto.sku"
                idLabel="SKU Producto"
            />

            <ProductoForm :form="form" :categorias="categorias" :laboratorios="laboratorios" :proveedores="proveedores" :is-editing="true" @submit="submit">
                <template #actions>
                    <div class="flex justify-end gap-4 pt-6 border-t border-border/50 mt-8">
                        <Link :href="ProductoMaestroController.index.url()" class="px-6 py-3 rounded-xl border border-border font-bold text-muted-foreground hover:bg-muted transition">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="bg-amber-500 text-white px-10 py-3 rounded-xl font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 transition-all uppercase tracking-widest text-xs">
                            {{ form.processing ? 'Guardando...' : 'Actualizar Información' }}
                        </button>
                    </div>
                </template>
            </ProductoForm>
        </AppSectionCard>

        <DeleteConfirmModal :show="mostrarModalEliminar" :itemName="producto.nombre_comercial" type="producto" @close="mostrarModalEliminar = false" @confirm="confirmarEliminacion" />
    </AppPageShell>
</template>