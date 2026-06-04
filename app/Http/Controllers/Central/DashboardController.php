<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\{ProductoMaestro, Lote, Transferencia, MovimientoInventario, Categoria};
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::now();
        $hace7Dias = Carbon::now()->subDays(7);


        $listaStockBajo = ProductoMaestro::whereRaw('
            (SELECT COALESCE(SUM(cantidad_actual), 0) 
             FROM central_lotes 
             WHERE central_lotes.producto_id = central_productos_maestro.id) <= stock_minimo
        ')->get(['id', 'nombre_comercial', 'sku', 'stock_minimo']);


        $listaVencimientos = Lote::with('producto')
            ->where('cantidad_actual', '>', 0)
            ->where('fecha_vencimiento', '<=', $hoy->copy()->addDays(90))
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();


        $listaPendientes = Transferencia::with('sedeDestino')
            ->where('estado', 'Pendiente')
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_productos' => ProductoMaestro::where('activo', true)->count(),
                'stock_bajo' => $listaStockBajo->count(),
                'por_vencer' => $listaVencimientos->count(),
                'transf_pendientes' => $listaPendientes->count(),
            ],
            'detalles' => [
                'stock_bajo' => $listaStockBajo,
                'vencimientos' => $listaVencimientos,
                'transferencias' => $listaPendientes
            ],
            'movimientosChart' => MovimientoInventario::select(
                    DB::raw('DATE(fecha_movimiento) as fecha'),
                    DB::raw('SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) as entradas'),
                    DB::raw('SUM(CASE WHEN cantidad < 0 THEN ABS(cantidad) ELSE 0 END) as salidas')
                )->where('fecha_movimiento', '>=', $hace7Dias)->groupBy('fecha')->orderBy('fecha')->get(),
            'categoriasChart' => Categoria::withCount('productos')->has('productos')->get(),
            'ultimosMovimientos' => MovimientoInventario::with(['lote.producto', 'usuario'])->latest('fecha_movimiento')->limit(5)->get()
        ]);
    }
}