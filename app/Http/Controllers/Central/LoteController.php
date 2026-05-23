<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\ProductoMaestro;
use Illuminate\Http\Request;
use Inertia\Inertia;
 
class LoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Lote::with('producto')
            ->orderBy('fecha_vencimiento');
 
        if ($request->filled('search')) {
            $query->where('numero_lote', 'like', "%{$request->search}%")
                  ->orWhereHas('producto', fn($q) =>
                      $q->where('nombre_comercial', 'like', "%{$request->search}%")
                  );
        }
 
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
 
        $lotes = $query->paginate(15)->withQueryString();
 
        return Inertia::render('Central/Lotes/Index', [
            'lotes'   => $lotes,
            'filters' => $request->only(['search', 'estado']),
            'estados' => ['Pendiente', 'En tránsito', 'Disponible', 'Agotado', 'Vencido'],
        ]);
    }
 
    public function create()
    {
        return Inertia::render('Central/Lotes/Create', [
            'productos' => ProductoMaestro::where('activo', true)
                ->orderBy('nombre_comercial')
                ->get(['id', 'nombre_comercial', 'sku']),
            'estados'   => ['Pendiente', 'En tránsito', 'Disponible', 'Agotado', 'Vencido'],
        ]);
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id'       => 'required|exists:central_productos_maestro,id',
            'numero_lote'       => 'required|string|max:100',
            'fecha_fabricacion' => 'nullable|date',
            'fecha_ingreso'     => 'required|date',
            'fecha_vencimiento' => 'required|date|after:today',
            'cantidad_inicial'  => 'required|integer|min:1',
            'cantidad_actual'   => 'required|integer|min:0',
            'costo_unitario'    => 'required|numeric|min:0',
            'estado'            => 'required|in:Pendiente,En tránsito,Disponible,Agotado,Vencido',
        ]);
 
        Lote::create($validated);
 
        return redirect()
            ->route('central.lotes.index')
            ->with('success', 'Lote creado correctamente.');
    }
 
    public function show(Lote $lote)
    {
        $lote->load('producto');
 
        return Inertia::render('Central/Lotes/Show', [
            'lote' => [
                'id'                => $lote->id,
                'numero_lote'       => $lote->numero_lote,
                'fecha_fabricacion' => $lote->fecha_fabricacion?->format('d/m/Y'),
                'fecha_ingreso'     => $lote->fecha_ingreso?->format('d/m/Y'),
                'fecha_vencimiento' => $lote->fecha_vencimiento?->format('d/m/Y'),
                'cantidad_inicial'  => $lote->cantidad_inicial,
                'cantidad_actual'   => $lote->cantidad_actual,
                'costo_unitario'    => $lote->costo_unitario,
                'estado'            => $lote->estado,
                'created_at'        => $lote->created_at?->format('d/m/Y H:i'),
                'producto'          => [
                    'id'              => $lote->producto->id,
                    'nombre_comercial'=> $lote->producto->nombre_comercial,
                    'sku'             => $lote->producto->sku,
                ],
            ],
        ]);
    }
 
    public function edit(Lote $lote)
    {
        return Inertia::render('Central/Lotes/Edit', [
            'lote' => [
                'id'                => $lote->id,
                'producto_id'       => $lote->producto_id,
                'numero_lote'       => $lote->numero_lote,
                'fecha_fabricacion' => $lote->fecha_fabricacion?->format('Y-m-d'),
                'fecha_ingreso'     => $lote->fecha_ingreso?->format('Y-m-d'),
                'fecha_vencimiento' => $lote->fecha_vencimiento?->format('Y-m-d'),
                'cantidad_inicial'  => $lote->cantidad_inicial,
                'cantidad_actual'   => $lote->cantidad_actual,
                'costo_unitario'    => $lote->costo_unitario,
                'estado'            => $lote->estado,
            ],
            'productos' => ProductoMaestro::where('activo', true)
                ->orderBy('nombre_comercial')
                ->get(['id', 'nombre_comercial', 'sku']),
            'estados'   => ['Pendiente', 'En tránsito', 'Disponible', 'Agotado', 'Vencido'],
        ]);
    }
 
    public function update(Request $request, Lote $lote)
    {
        $validated = $request->validate([
            'producto_id'       => 'required|exists:central_productos_maestro,id',
            'numero_lote'       => 'required|string|max:100',
            'fecha_fabricacion' => 'nullable|date',
            'fecha_ingreso'     => 'required|date',
            'fecha_vencimiento' => 'required|date',
            'cantidad_inicial'  => 'required|integer|min:1',
            'cantidad_actual'   => 'required|integer|min:0',
            'costo_unitario'    => 'required|numeric|min:0',
            'estado'            => 'required|in:Pendiente,En tránsito,Disponible,Agotado,Vencido',
        ]);
 
        $lote->update($validated);
 
        return redirect()
            ->route('central.lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }
 
    public function destroy(Lote $lote)
    {
        $lote->delete();
 
        return redirect()
            ->route('central.lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}