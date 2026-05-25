<script setup lang="ts">
interface Option { id: number; nombre?: string; razon_social?: string }

const props = defineProps<{
    form: {
        sku: string
        nombre_comercial: string
        nombre_generico: string
        descripcion: string
        categoria_id: number | null
        laboratorio_id: number | null
        proveedor_id: number | null
        requiere_receta: boolean
        registro_sanitario: string
        concentracion: string
        forma_farmaceutica: string
        unidad_medida: string
        stock_minimo: number
        activo: boolean
        errors: Record<string, string>
    }
    categorias: Option[]
    laboratorios: Option[]
    proveedores: Option[]
    isEditing?: boolean
}>()

const emit = defineEmits<{ submit: [] }>()

// Estilo común para los labels técnicos
const labelClass = "block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-2"
// Estilo común para inputs
const inputClass = "w-full h-11 rounded-lg border-border bg-background px-4 text-sm text-foreground focus:ring-2 focus:ring-primary/50 outline-none transition-all placeholder:text-muted-foreground/40"
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-8">

        <!-- SECCIÓN 1: Identificación -->
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
                    :class="[inputClass, isEditing ? 'opacity-50 cursor-not-allowed bg-muted' : '']"
                />
                <p v-if="form.errors.sku" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.sku }}</p>
            </div>

            <div class="md:col-span-4 flex items-end pb-3">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input v-model="form.activo" type="checkbox" class="peer h-5 w-5 rounded border-border bg-background text-primary focus:ring-primary/20" />
                    </div>
                    <span class="text-xs font-bold text-muted-foreground group-hover:text-white transition-colors uppercase tracking-widest">Producto Activo</span>
                </label>
            </div>
            
            <div class="md:col-span-4 flex items-end pb-3">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input v-model="form.requiere_receta" type="checkbox" class="peer h-5 w-5 rounded border-border bg-background text-orange-500 focus:ring-orange-500/20" />
                    </div>
                    <span class="text-xs font-bold text-muted-foreground group-hover:text-orange-400 transition-colors uppercase tracking-widest">Requiere Receta</span>
                </label>
            </div>
        </div>

        <!-- SECCIÓN 2: Información Comercial -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label :class="labelClass">Nombre Comercial <span class="text-destructive">*</span></label>
                <input v-model="form.nombre_comercial" type="text" placeholder="Nombre en caja" :class="inputClass" />
                <p v-if="form.errors.nombre_comercial" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.nombre_comercial }}</p>
            </div>
            <div>
                <label :class="labelClass">Nombre Genérico (Componente)</label>
                <input v-model="form.nombre_generico" type="text" placeholder="Ej: Paracetamol" :class="inputClass" />
            </div>
        </div>

        <!-- SECCIÓN 3: Relaciones -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-muted/20 rounded-xl border border-border/50">
            <div>
                <label :class="labelClass">Categoría <span class="text-destructive">*</span></label>
                <select v-model="form.categoria_id" :class="inputClass">
                    <option :value="null">Seleccionar...</option>
                    <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                </select>
                <p v-if="form.errors.categoria_id" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.categoria_id }}</p>
            </div>
            <div>
                <label :class="labelClass">Laboratorio <span class="text-destructive">*</span></label>
                <select v-model="form.laboratorio_id" :class="inputClass">
                    <option :value="null">Seleccionar...</option>
                    <option v-for="l in laboratorios" :key="l.id" :value="l.id">{{ l.nombre }}</option>
                </select>
                <p v-if="form.errors.laboratorio_id" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.laboratorio_id }}</p>
            </div>
            <div>
                <label :class="labelClass">Proveedor Principal <span class="text-destructive">*</span></label>
                <select v-model="form.proveedor_id" :class="inputClass">
                    <option :value="null">Seleccionar...</option>
                    <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.razon_social }}</option>
                </select>
                <p v-if="form.errors.proveedor_id" class="mt-2 text-xs text-red-500 font-medium">{{ form.errors.proveedor_id }}</p>
            </div>
        </div>

        <!-- SECCIÓN 4: Especificaciones Técnicas -->
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

        <!-- Botones de Acción -->
        <div class="flex justify-end gap-3 pt-4">
            <slot name="actions" />
        </div>

    </form>
</template>