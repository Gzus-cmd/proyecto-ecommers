<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\{Transferencia, Sede, Lote, ProductoMaestro, MovimientoInventario};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransferenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Transferencia::with(['sedeDestino'])->withCount('detalles')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('estado', 'like', "%{$request->search}%")
                  ->orWhereHas('sedeDestino', fn($q) => $q->where('nombre', 'like', "%{$request->search}%"));
        }

        return Inertia::render('Central/Transferencias/Index', [
            'transferencias' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Central/Transferencias/Create', [
            'sedes' => Sede::where('activo', true)->get(['id', 'nombre']),
            'productosConLotes' => ProductoMaestro::where('activo', true)
                ->with(['lotes' => fn($q) => $q->where('cantidad_actual', '>', 0)])
                ->whereHas('lotes', fn($q) => $q->where('cantidad_actual', '>', 0))
                ->get(['id', 'nombre_comercial'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sede_destino_id' => 'required|exists:central_sedes,id',
            'fecha_envio'     => 'required|date',
            'detalles'        => 'required|array|min:1',
            'detalles.*.lote_id' => 'required|exists:central_lotes,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        $transferencia = Transferencia::create([
            'sede_destino_id' => $validated['sede_destino_id'],
            'fecha_envio'     => $validated['fecha_envio'],
            'estado'          => 'Pendiente', 
            'observaciones'   => $request->observaciones,
        ]);

        foreach ($validated['detalles'] as $item) {
            $transferencia->detalles()->create([
                'lote_id' => $item['lote_id'],
                'cantidad' => $item['cantidad'],
            ]);
        }

        return redirect()->route('central.transferencias.index')
            ->with('success', 'Transferencia guardada correctamente.');
    }

public function show(Request $request, Transferencia $transferencia)
{
    $data = $transferencia->load([
        'sedeDestino', 
        'detalles.lote.producto', 
        'movimientos.lote', 
        'movimientos.usuario'
    ]);

    // Si la petición viene de un modal (XHR), devolvemos solo el JSON
    if ($request->wantsJson()) {
        return response()->json($data);
    }

    // Si es navegación normal, renderizamos la página (como antes)
    return Inertia::render('Central/Transferencias/Show', [
        'transferencia' => $data
    ]);
}

    public function edit(Transferencia $transferencia)
    {
        if ($transferencia->estado !== 'Pendiente') {
            return redirect()->route('central.transferencias.index')
                ->with('error', 'No se puede editar una transferencia ya enviada.');
        }

        return Inertia::render('Central/Transferencias/Edit', [
            'transferencia' => $transferencia->load('detalles.lote.producto'),
            'sedes' => Sede::where('activo', true)->get(['id', 'nombre']),
            'productosConLotes' => ProductoMaestro::where('activo', true)
                ->with(['lotes' => fn($q) => $q->where('cantidad_actual', '>', 0)])
                ->whereHas('lotes', fn($q) => $q->where('cantidad_actual', '>', 0))
                ->get(['id', 'nombre_comercial'])
        ]);
    }

    public function update(Request $request, Transferencia $transferencia)
    {
        $validated = $request->validate([
            'sede_destino_id' => 'required|exists:central_sedes,id',
            'fecha_envio'     => 'required|date',
            'detalles'        => 'required|array|min:1',
            'detalles.*.lote_id' => 'required|exists:central_lotes,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        $transferencia->update([
            'sede_destino_id' => $validated['sede_destino_id'],
            'fecha_envio'     => $validated['fecha_envio'],
            'observaciones'   => $request->observaciones,
        ]);

        $transferencia->detalles()->delete();
        foreach ($validated['detalles'] as $item) {
            $transferencia->detalles()->create($item);
        }

        return redirect()->route('central.transferencias.index')
            ->with('success', 'Transferencia actualizada.');
    }

    public function enviar(Transferencia $transferencia)
    {
        if ($transferencia->estado !== 'Pendiente') return back();

        try {
            DB::transaction(function () use ($transferencia) {
                foreach ($transferencia->detalles as $detalle) {
                    $lote = Lote::lockForUpdate()->findOrFail($detalle->lote_id);
                    if ($lote->cantidad_actual < $detalle->cantidad) throw new \Exception("Sin stock.");

                    $stockAntes = $lote->cantidad_actual;
                    $lote->decrement('cantidad_actual', $detalle->cantidad);

                    MovimientoInventario::create([
                        'lote_id' => $lote->id,
                        'tipo_movimiento_id' => 1,
                        'movimentable_id' => $transferencia->id,
                        'movimentable_type' => Transferencia::class,
                        'cantidad' => -$detalle->cantidad,
                        'stock_antes' => $stockAntes,
                        'stock_despues' => $lote->cantidad_actual,
                        'usuario_id' => auth()->id(),
                    ]);
                }
                $transferencia->update(['estado' => 'Enviado']);
            });
            return back()->with('success', 'Enviado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}