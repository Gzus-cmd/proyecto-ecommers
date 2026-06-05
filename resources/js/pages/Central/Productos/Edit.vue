<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { Trash2, Package } from 'lucide-vue-next'
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
            { title: 'Inventario', href: '#' },
            { title: 'Productos Maestro', href: ProductoMaestroController.index.url() },
            { title: 'Editar Ficha', href: '#' },
        ],
    },
});

const props = defineProps<{ 
    producto: any, 
    categorias: any[], 
    laboratorios: any[], 
    proveedores: any[] 
}>()


const form = useForm({
    sku: props.producto.sku,
    nombre_comercial: props.producto.nombre_comercial,
    nombre_generico: props.producto.nombre_generico ?? '',
    descripcion: props.producto.descripcion ?? '',
    categoria_id: props.producto.categoria_id,
    laboratorio_id: props.producto.laboratorio_id,
    proveedor_id: props.producto.proveedor_id,
    requiere_receta: !!props.producto.requiere_receta,
    registro_sanitario: props.producto.registro_sanitario ?? '',
    concentracion: props.producto.concentracion ?? '',
    forma_farmaceutica: props.producto.forma_farmaceutica ?? '',
    unidad_medida: props.producto.unidad_medida ?? '',
    stock_minimo: props.producto.stock_minimo ?? 0,
    activo: !!props.producto.activo,
})


const mostrarModalEliminar = ref(false)
const confirmarEliminacion = () => {
    router.delete(ProductoMaestroController.destroy.url(props.producto.id), {
        onSuccess: () => mostrarModalEliminar.value = false
    })
}

const submit = () => {
    form.put(ProductoMaestroController.update.url(props.producto.id), {
        preserveScroll: true
    })
}
</script>

<template>
    <AppPageShell :title="'Editar: ' + producto.nombre_comercial" variant="narrow">


        <AppPageHeader 
            title="Editar Producto" 
            subtitle="Gestión de especificaciones técnicas y regulatorias."
            :backUrl="ProductoMaestroController.index.url()"
        >
            <template #actions>
                <button
                    type="button"
                    @click="mostrarModalEliminar = true"
                    class="bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-2"
                >
                    <Trash2 class="size-3.5" /> Dar de Baja
                </button>
            </template>
        </AppPageHeader>

        <AppSectionCard>
            

            <AppEditContextCard 
                title="Editando Producto Maestro"
                :subtitle="producto.nombre_comercial"
                :itemId="producto.sku"
                idLabel="Código SKU"
                :icon="Package"
            />


            <form @submit.prevent="submit" class="space-y-0">
                <ProductoForm 
                    :form="form" 
                    :categorias="categorias" 
                    :laboratorios="laboratorios" 
                    :proveedores="proveedores"
                    :isEditing="true"
                >
                    <template #actions>

                        <div class="flex justify-end gap-4 pt-6 border-t border-border/50">
                            <Link
                                :href="ProductoMaestroController.index.url()"
                                class="px-6 py-2.5 rounded-lg border border-border text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-muted transition"
                            >
                                Anular Cambios
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-amber-500 text-white px-10 py-2.5 rounded-lg text-[10px] font-black shadow-lg shadow-amber-500/20 hover:bg-amber-600 disabled:opacity-50 transition-all uppercase tracking-widest"
                            >
                                {{ form.processing ? 'Sincronizando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </template>
                </ProductoForm>
            </form>
        </AppSectionCard>


        <DeleteConfirmModal 
            :show="mostrarModalEliminar"
            :itemName="producto.nombre_comercial"
            type="producto del catálogo"
            @close="mostrarModalEliminar = false"
            @confirm="confirmarEliminacion"
        />

    </AppPageShell>
</template>