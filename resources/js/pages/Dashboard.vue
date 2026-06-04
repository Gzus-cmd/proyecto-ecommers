<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Package, AlertTriangle, CalendarClock, Truck, ArrowRight, X, Info, ExternalLink } from 'lucide-vue-next'
import { Bar, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

// Componentes estructurales de PharmaVictoria
import AppPageShell from '@/components/app/AppPageShell.vue'
import AppPageHeader from '@/components/app/AppPageHeader.vue'
import AppSectionCard from '@/components/app/AppSectionCard.vue'
import AppStatusBadge from '@/components/app/AppStatusBadge.vue'

// Importación Wayfinder
import * as MovimientoInventarioController from '@/actions/App/Http/Controllers/Central/MovimientoInventarioController'
import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController'
import * as ProductoController from '@/actions/App/Http/Controllers/Central/ProductoMaestroController'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const props = defineProps<{ 
    stats: any, 
    detalles: any,
    movimientosChart: any[], 
    categoriasChart: any[],
    ultimosMovimientos: any[] 
}>()

// --- LÓGICA DE MODALES DE DETALLE ---
const modalVisible = ref(false)
const tituloModal = ref('')
const listaModal = ref<any[]>([])
const tipoActivo = ref('') // 'stock' | 'vence' | 'transf'

function abrirDetalle(tipo: string) {
    tipoActivo.value = tipo
    if (tipo === 'stock') {
        tituloModal.value = 'Productos con Stock Crítico'
        listaModal.value = props.detalles.stock_bajo
    } else if (tipo === 'vence') {
        tituloModal.value = 'Lotes Próximos a Vencer (90 días)'
        listaModal.value = props.detalles.vencimientos
    } else if (tipo === 'transf') {
        tituloModal.value = 'Transferencias en Espera'
        listaModal.value = props.detalles.transferencias
    }
    modalVisible.value = true
}

// --- CONFIGURACIÓN DE GRÁFICOS ---
const barData = computed(() => ({
    labels: props.movimientosChart.map(m => m.fecha),
    datasets: [
        { label: 'Entradas', backgroundColor: '#10b981', borderRadius: 4, data: props.movimientosChart.map(m => m.entradas) },
        { label: 'Salidas', backgroundColor: '#ef4444', borderRadius: 4, data: props.movimientosChart.map(m => m.salidas) }
    ]
}))

const doughnutData = computed(() => ({
    labels: props.categoriasChart.map(c => c.nombre),
    datasets: [{
        backgroundColor: ['#5790AB', '#9CCDDB', '#064469', '#10b981', '#f59e0b'],
        borderWidth: 0,
        data: props.categoriasChart.map(c => c.productos_count)
    }]
}))

const chartOptions: any = { 
    responsive: true, 
    maintainAspectRatio: false, 
    plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10, weight: 'bold' }, usePointStyle: true } } },
    scales: { x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }, y: { display: false } }
}
</script>

<template>
    <AppPageShell title="Dashboard" variant="full">
        
        <AppPageHeader 
            title="Panel de Control" 
            subtitle="Bienvenido al centro de monitoreo de PharmaVictoria."
        />

        <!-- 1. KPIs INTERACTIVOS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card: Total Productos (Informativa) -->
            <AppSectionCard class="border-l-4 border-l-blue-500 shadow-lg">
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500"><Package class="size-6" /></div>
                    <span class="text-3xl font-black text-foreground tracking-tighter">{{ stats.total_productos }}</span>
                </div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">Productos Activos</p>
            </AppSectionCard>

            <!-- Card: Stock Bajo (Clickable) -->
            <AppSectionCard @click="abrirDetalle('stock')" class="border-l-4 border-l-red-500 shadow-lg cursor-pointer hover:bg-red-500/5 transition-all group">
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-red-500/10 rounded-xl text-red-500 group-hover:scale-110 transition-transform"><AlertTriangle class="size-6" /></div>
                    <span class="text-3xl font-black text-foreground tracking-tighter">{{ stats.stock_bajo }}</span>
                </div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-red-500/70">Stock Bajo Mínimo</p>
            </AppSectionCard>

            <!-- Card: Vencimientos (Clickable) -->
            <AppSectionCard @click="abrirDetalle('vence')" class="border-l-4 border-l-amber-500 shadow-lg cursor-pointer hover:bg-amber-500/5 transition-all group">
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-amber-500/10 rounded-xl text-amber-500 group-hover:scale-110 transition-transform"><CalendarClock class="size-6" /></div>
                    <span class="text-3xl font-black text-foreground tracking-tighter">{{ stats.por_vencer }}</span>
                </div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-amber-500/70">Próximos a Vencer</p>
            </AppSectionCard>

            <!-- Card: Transferencias (Clickable) -->
            <AppSectionCard @click="abrirDetalle('transf')" class="border-l-4 border-l-emerald-500 shadow-lg cursor-pointer hover:bg-emerald-500/5 transition-all group">
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 group-hover:scale-110 transition-transform"><Truck class="size-6" /></div>
                    <span class="text-3xl font-black text-foreground tracking-tighter">{{ stats.transf_pendientes }}</span>
                </div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">Transf. Pendientes</p>
            </AppSectionCard>
        </div>

        <!-- 2. ANÁLISIS DE GRÁFICOS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <AppSectionCard title="Flujo Semanal de Almacén" class="lg:col-span-2">
                <div class="h-72 w-full mt-4">
                    <Bar v-if="movimientosChart.length" :data="barData" :options="chartOptions" />
                    <div v-else class="h-full flex items-center justify-center text-muted-foreground text-[10px] uppercase tracking-widest italic">Sin movimientos recientes</div>
                </div>
            </AppSectionCard>

            <AppSectionCard title="Distribución de Stock">
                <div class="h-72 w-full mt-4 flex items-center justify-center">
                    <Doughnut v-if="categoriasChart.length" :data="doughnutData" :options="chartOptions" />
                    <div v-else class="h-full flex items-center justify-center text-muted-foreground text-[10px] uppercase tracking-widest italic">Sin categorías registradas</div>
                </div>
            </AppSectionCard>
        </div>

        <!-- 3. ACTIVIDAD RECIENTE (TABLA ESTIRADA) -->
        <AppSectionCard fill noPadding title="Últimos Movimientos del Kardex">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-muted/30 text-muted-foreground text-[10px] font-black uppercase tracking-widest border-b border-border">
                        <tr>
                            <th class="px-8 py-5">Fecha / Responsable</th>
                            <th class="px-8 py-5">Medicamento / Lote</th>
                            <th class="px-8 py-5 text-center">Variación</th>
                            <th class="px-8 py-5 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-foreground font-medium">
                        <tr v-for="m in ultimosMovimientos" :key="m.id" class="hover:bg-muted/10 transition-colors">
                            <td class="px-8 py-5">
                                <div class="font-bold text-xs">{{ m.fecha_movimiento }}</div>
                                <div class="text-[10px] text-muted-foreground uppercase tracking-tighter">{{ m.usuario.name }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-base">{{ m.lote.producto.nombre_comercial }}</div>
                                <div class="text-[10px] text-muted-foreground font-mono uppercase tracking-widest">Lote: {{ m.lote.numero_lote }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span :class="[m.cantidad > 0 ? 'text-emerald-500 bg-emerald-500/10' : 'text-red-500 bg-red-500/10', 'px-4 py-1 rounded-lg font-black text-sm border border-current/10 shadow-sm']">
                                    {{ m.cantidad > 0 ? '+' : '' }}{{ m.cantidad }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <Link :href="MovimientoInventarioController.index.url()" 
                                      class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-primary hover:text-foreground transition-all">
                                    Ver Kardex <ArrowRight class="size-3" />
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </AppSectionCard>

        <!-- --- MODAL DE DETALLE DINÁMICO --- -->
        <div v-if="modalVisible" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-card w-full max-w-2xl rounded-2xl border border-border/50 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                
                <div class="px-8 py-5 border-b border-border/50 bg-muted/20 flex justify-between items-center">
                    <h3 class="text-sm font-black uppercase text-foreground tracking-[0.2em]">{{ tituloModal }}</h3>
                    <button @click="modalVisible = false" class="text-muted-foreground hover:text-foreground p-2 transition-colors">
                        <X class="size-5" />
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-border/50 text-foreground">
                            
                            <!-- Caso Stock Bajo -->
                            <template v-if="tipoActivo === 'stock'">
                                <tr v-for="p in listaModal" :key="p.id" class="hover:bg-muted/10 transition-colors">
                                    <td class="py-4">
                                        <div class="font-bold text-base">{{ p.nombre_comercial }}</div>
                                        <div class="text-[10px] text-muted-foreground font-mono uppercase tracking-widest">{{ p.sku }}</div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="text-red-500 font-black uppercase text-[10px] bg-red-500/10 px-3 py-1 rounded-lg border border-red-500/20">
                                            Alerta: Stock &lt; {{ p.stock_minimo }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <Link :href="ProductoController.show.url(p.id)" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors inline-block"><ExternalLink class="size-4" /></Link>
                                    </td>
                                </tr>
                            </template>

                            <!-- Caso Vencimientos -->
                            <template v-else-if="tipoActivo === 'vence'">
                                <tr v-for="l in listaModal" :key="l.id" class="hover:bg-muted/10 transition-colors">
                                    <td class="py-4">
                                        <div class="font-bold text-base">{{ l.producto.nombre_comercial }}</div>
                                        <div class="text-[10px] text-muted-foreground uppercase font-mono tracking-widest">Lote: {{ l.numero_lote }}</div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="text-amber-500 font-black text-xs uppercase tracking-tighter bg-amber-500/10 px-3 py-1 rounded-lg border border-amber-500/20">
                                            Vence: {{ l.fecha_vencimiento }}
                                        </span>
                                    </td>
                                </tr>
                            </template>

                            <!-- Caso Transferencias -->
                            <template v-else-if="tipoActivo === 'transf'">
                                <tr v-for="t in listaModal" :key="t.id" class="hover:bg-muted/10 transition-colors">
                                    <td class="py-4">
                                        <div class="font-bold text-base">Hacia: {{ t.sede_destino.nombre }}</div>
                                        <div class="text-[10px] text-muted-foreground uppercase tracking-widest">Fecha: {{ t.fecha_envio }}</div>
                                    </td>
                                    <td class="py-4 text-right">
                                        <Link :href="TransferenciaController.show.url(t.id)" class="bg-primary/10 text-primary font-black uppercase text-[10px] tracking-widest px-4 py-2 rounded-lg border border-primary/20 hover:bg-primary hover:text-white transition-all">Ver Hoja de Ruta</Link>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-5 border-t border-border/50 bg-muted/10 flex justify-end">
                    <button @click="modalVisible = false" class="bg-foreground text-background px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 shadow-md">
                        Cerrar Monitor
                    </button>
                </div>
            </div>
        </div>

    </AppPageShell>
</template>