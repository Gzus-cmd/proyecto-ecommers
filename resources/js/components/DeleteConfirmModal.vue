<script setup lang="ts">
import { AlertTriangle } from 'lucide-vue-next'

// Definimos lo que el modal necesita recibir del padre
defineProps<{
    show: boolean;         // Controla si se ve o no
    title?: string;        // Título del modal
    itemName?: string;     // Nombre del objeto a borrar (ej: "Ibuprofeno")
    type?: string;         // Tipo de objeto (ej: "producto", "sede")
    processing?: boolean;  // Estado de carga del botón
}>()

// Definimos los eventos que el modal emite al padre
const emit = defineEmits(['close', 'confirm'])
</script>

<template>
    <!-- Fondo con desenfoque -->
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        
        <!-- Caja del Modal -->
        <div class="bg-card w-full max-w-md rounded-2xl border border-red-500/20 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            
            <div class="p-8 text-center">
                <!-- Icono de Advertencia -->
                <div class="mx-auto w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center text-red-500 mb-4 border border-red-500/20">
                    <AlertTriangle class="size-8" />
                </div>
                
                <!-- Textos Dinámicos -->
                <h3 class="text-xl font-black text-white uppercase tracking-tight mb-2">
                    {{ title || 'Confirmar Eliminación' }}
                </h3>
                
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Está a punto de dar de baja este {{ type || 'registro' }}: <br>
                    <span class="text-white font-bold text-base">"{{ itemName }}"</span>
                </p>

                <!-- Aviso Técnico -->
                <div class="mt-4 p-3 bg-red-500/5 rounded-lg border border-red-500/10">
                    <p class="text-[9px] font-black text-red-400 uppercase tracking-[0.2em]">
                        ⚠ Esta acción es irreversible en el sistema
                    </p>
                </div>
            </div>

            <!-- Botonera Inferior -->
            <div class="grid grid-cols-2 border-t border-border/50">
                <button 
                    @click="emit('close')" 
                    type="button"
                    class="p-4 text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-muted transition-colors border-r border-border/50"
                >
                    Anular
                </button>
                <button 
                    @click="emit('confirm')" 
                    type="button"
                    :disabled="processing"
                    class="p-4 text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-500 hover:text-white transition-all disabled:opacity-50"
                >
                    {{ processing ? 'Procesando...' : 'Confirmar Baja' }}
                </button>
            </div>
        </div>
    </div>
</template>