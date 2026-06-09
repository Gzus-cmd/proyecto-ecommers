<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { ShieldAlert, ArrowLeft, Lock } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

const props = defineProps<{ status: number }>()

const title = computed(() => {
  return {
    403: '403: ACCESO RESTRINGIDO',
    404: '404: RECURSO NO ENCONTRADO',
    500: '500: ERROR INTERNO DEL SISTEMA',
    503: '503: SERVICIO NO DISPONIBLE',
  }[props.status] || 'ERROR DE SISTEMA'
})

const description = computed(() => {
  return {
    403: 'Tus credenciales actuales no tienen los privilegios necesarios para ejecutar esta acción en el ecosistema PharmaVictoria.',
    404: 'La terminal solicitada no existe o ha sido movida permanentemente.',
    500: 'Ha ocurrido una falla crítica en el servidor. El equipo técnico ha sido notificado.',
    503: 'El sistema se encuentra en mantenimiento programado. Reintente en unos minutos.',
  }[props.status] || 'Ha ocurrido un error inesperado.'
})
</script>

<template>
  <Head :title="title" />
  <div class="min-h-screen bg-[#020817] flex items-center justify-center p-6 border-[12px] border-double border-blue-900/20">
    <div class="max-w-md w-full text-center space-y-8 animate-in fade-in zoom-in duration-500">
      

      <div class="relative mx-auto w-24 h-24 bg-blue-950 rounded-2xl flex items-center justify-center border border-blue-500/30 shadow-[0_0_50px_-12px_rgba(59,130,246,0.5)]">
        <Lock class="size-12 text-blue-400" />
        <div class="absolute -inset-1 bg-blue-500/20 rounded-2xl animate-pulse blur-xl"></div>
      </div>


      <div class="space-y-4">
        <h1 class="text-white font-black text-2xl uppercase tracking-[0.3em]">
          {{ title }}
        </h1>
        <div class="h-1 w-20 bg-blue-600 mx-auto rounded-full"></div>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest leading-relaxed px-4">
          {{ description }}
        </p>
      </div>


      <div class="pt-8">
        <Link href="/dashboard">
          <Button class="bg-blue-900 hover:bg-blue-800 text-white font-black uppercase text-[10px] tracking-[0.3em] h-12 px-8 rounded-xl border border-blue-400/30 transition-all active:scale-95">
            <ArrowLeft class="mr-2 size-4" /> Volver a la Consola
          </Button>
        </Link>
      </div>


      <div class="pt-12 text-[9px] font-black text-slate-600 uppercase tracking-[0.4em]">
        PharmaVictoria Security Protocol v1.0
      </div>
    </div>
  </div>
</template>