<script setup lang="ts">
import { AlertTriangle, ShieldAlert } from 'lucide-vue-next'
import { useAuth } from '@/composables/useAuth';

// 1. Props del sistema original
const props = defineProps<{
    show: boolean;         
    title?: string;        
    itemName?: string;     
    type?: string;         
    processing?: boolean;  
}>()

const emit = defineEmits(['close', 'confirm'])

// 2. Lógica de seguridad
const { can } = useAuth();
const hasPermission = can('maestros.delete');
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-100 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        
        <div class="bg-card w-full max-w-md rounded-2xl border border-border shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            
            <!-- ESTADO A: Usuario Autorizado (Admin) -->
            <template v-if="hasPermission">
                <div class="p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center text-red-500 mb-4 border border-red-500/20">
                        <AlertTriangle class="size-8" />
                    </div>
                    
                    <h3 class="text-xl font-black text-white uppercase tracking-tight mb-2">
                        {{ title || 'Confirmar Eliminación' }}
                    </h3>
                    
                    <p class="text-sm text-muted-foreground leading-relaxed">
                        Está a punto de dar de baja este {{ type || 'registro' }}: <br>
                        <span class="text-white font-bold text-base">"{{ itemName }}"</span>
                    </p>

                    <div class="mt-4 p-3 bg-red-500/5 rounded-lg border border-red-500/10">
                        <p class="text-[9px] font-black text-red-400 uppercase tracking-[0.2em]">
                            ⚠ Esta acción es irreversible en el sistema
                        </p>
                    </div>
                </div>

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
            </template>

            <!-- ESTADO B: Acceso Denegado (Auxiliar / Jefe) -->
            <template v-else>
                <div class="p-10 text-center">
                    <div class="mx-auto w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center text-amber-500 mb-4 border border-amber-500/20">
                        <ShieldAlert class="size-8" />
                    </div>
                    
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">
                        Acceso Restringido
                    </h3>
                    
                    <p class="text-[11px] text-muted-foreground uppercase tracking-wider leading-relaxed">
                        No posees credenciales de nivel <span class="text-amber-500">Administrador Central</span> para ejecutar bajas en el sistema maestro.
                    </p>

                    <button 
                        @click="emit('close')" 
                        class="mt-8 w-full p-4 bg-muted text-[10px] font-black uppercase tracking-[0.3em] text-white hover:bg-border transition-colors rounded-lg"
                    >
                        Regresar a la consola
                    </button>
                </div>
            </template>

        </div>
    </div>
</template>