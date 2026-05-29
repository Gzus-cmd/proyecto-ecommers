<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = MovimientoInventario::with(['lote.producto', 'tipo', 'usuario', 'movimentable'])
            ->orderBy('fecha_movimiento', 'desc');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lote.producto', function($q) use ($search) {
                $q->where('nombre_comercial', 'like', "%{$search}%");
            })->orWhereHas('lote', function($q) use ($search) {
                $q->where('numero_lote', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Central/Movimientos/Index', [
            'movimientos' => $query->paginate(20)->withQueryString(),
            'filters'     => $request->only(['search']),
        ]);
    }
}