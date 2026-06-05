<script setup lang="ts">

interface Option { id: number; nombre?: string; razon_social?: string }

const props = defineProps<{
    form: any 
    categorias: Option[]
    laboratorios: Option[]
    proveedores: Option[]
    isEditing?: boolean
}>()


const labelClass = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2"

const inputClass = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40"
</script>

<template>

    <div class="space-y-8">


        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-4">
                <label :class="labelClass">
                    SKU del Producto <span class="text-destructive">*</span>
                </label>
                <input
                    v-model="form.sku" 
                    type="text"
                    :disabled="isEditing"
                    placeholder="Ej: MED-001"
                    :class="[inputClass, isEditing ? 'opacity-50 cursor-not-allowed bg-muted' : '', form.errors.sku ? 'border-destructive' : '']"
                />
                <p v-if="form.errors.sku" class="mt-2 text-[10px] text-destructive font-bold uppercase tracking-widest">{{ form.errors.sku }}</p>
            </div>

            <div class="md:col-span-4 flex items-end pb-3">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input v-model="form.activo" type="checkbox" class="h-5 w-5 rounded border-border bg-background text-primary focus:ring-primary/20" />
                    <span class="text-[10px] font-black text-muted-foreground group-hover:text-foreground transition-colors uppercase tracking-[0.2em]">Producto Activo</span>
                </label>
            </div>
            
            <div class="md:col-span-4 flex items-end pb-3">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input v-model="form.requiere_receta" type="checkbox" class="h-5 w-5 rounded border-border bg-background text-orange-500 focus:ring-orange-500/20" />
                    <span class="text-[10px] font-black text-muted-foreground group-hover:text-orange-400 transition-colors uppercase tracking-[0.2em]">Requiere Receta</span>
                </label>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label :class="labelClass">Nombre Comercial <span class="text-destructive">*</span></label>
                <input v-model="form.nombre_comercial" type="text" placeholder="Nombre en caja" :class="[inputClass, form.errors.nombre_comercial ? 'border-destructive' : '']" />
                <p v-if="form.errors.nombre_comercial" class="mt-2 text-[10px] text-destructive font-bold uppercase tracking-widest">{{ form.errors.nombre_comercial }}</p>
            </div>
            <div>
                <label :class="labelClass">Nombre Genérico (Componente)</label>
                <input v-model="form.nombre_generico" type="text" placeholder="Ej: Paracetamol" :class="inputClass" />
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-muted/20 rounded-xl border border-border/50">
            <div>
                <label :class="labelClass">Categoría <span class="text-destructive">*</span></label>
                <select v-model="form.categoria_id" :class="[inputClass, form.errors.categoria_id ? 'border-destructive' : '']">
                    <option :value="null">Seleccionar...</option>
                    <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                </select>
                <p v-if="form.errors.categoria_id" class="mt-2 text-[10px] text-destructive font-bold uppercase tracking-widest">{{ form.errors.categoria_id }}</p>
            </div>
            <div>
                <label :class="labelClass">Laboratorio <span class="text-destructive">*</span></label>
                <select v-model="form.laboratorio_id" :class="[inputClass, form.errors.laboratorio_id ? 'border-destructive' : '']">
                    <option :value="null">Seleccionar...</option>
                    <option v-for="l in laboratorios" :key="l.id" :value="l.id">{{ l.nombre }}</option>
                </select>
            </div>
            <div>
                <label :class="labelClass">Proveedor Principal <span class="text-destructive">*</span></label>
                <select v-model="form.proveedor_id" :class="[inputClass, form.errors.proveedor_id ? 'border-destructive' : '']">
                    <option :value="null">Seleccionar...</option>
                    <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.razon_social }}</option>
                </select>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-4">
                <label :class="labelClass">Descripción y Uso</label>
                <textarea v-model="form.descripcion" rows="2" class="w-full rounded-lg border-border bg-background px-4 py-3 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all" placeholder="Detalles adicionales del producto..."></textarea>
            </div>
            
            <div>
                <label :class="labelClass">Concentración</label>
                <input v-model="form.concentracion" type="text" placeholder="Ej: 500mg" :class="inputClass" />
            </div>
            <div>
                <label :class="labelClass">Forma Farm.</label>
                <input v-model="form.forma_farmaceutica" type="text" placeholder="Ej: Tableta" :class="inputClass" />
            </div>
            <div>
                <label :class="labelClass">Reg. Sanitario</label>
                <input v-model="form.registro_sanitario" type="text" placeholder="Código DIGEMID" :class="inputClass" />
            </div>
            <div>
                <label :class="labelClass">Stock Mínimo</label>
                <input v-model.number="form.stock_minimo" type="number" min="0" :class="inputClass" />
            </div>
        </div>


        <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
            <slot name="actions" />
        </div>

    </div>
</template>