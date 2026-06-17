<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class LaboratorioController extends Controller
{
    use AuthorizesRequests; 


    public function index(Request $request)
    {
        $this->authorize('inventario.view');

        $query = Laboratorio::orderBy('nombre');
 
        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%")
                  ->orWhere('pais', 'like', "%{$request->search}%");
        }
 
        $laboratorios = $query->paginate(15)->withQueryString();
 
        return Inertia::render('Central/Laboratorios/Index', [
            'laboratorios' => $laboratorios,
            'filters'      => $request->only(['search']),
        ]);
    }
 

    public function create()
    {
        $this->authorize('maestros.create');

        return Inertia::render('Central/Laboratorios/Create');
    }
 

    public function store(Request $request)
    {
        $this->authorize('maestros.create');

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:central_laboratorios,nombre',
            'pais'   => 'nullable|string|max:100',
        ]);
 
        Laboratorio::create($validated);
 
        return redirect()
            ->route('central.laboratorios.index')
            ->with('success', 'Laboratorio registrado exitosamente en el sistema.');
    }
 

    public function edit(Laboratorio $laboratorio)
    {
        $this->authorize('maestros.update');

        return Inertia::render('Central/Laboratorios/Edit', [
            'laboratorio' => $laboratorio,
        ]);
    }
 

    public function update(Request $request, Laboratorio $laboratorio)
    {
        $this->authorize('maestros.update');

        $validated = $request->validate([
            'nombre' => "required|string|max:255|unique:central_laboratorios,nombre,{$laboratorio->id}",
            'pais'   => 'nullable|string|max:100',
        ]);
 
        $laboratorio->update($validated);
 
        return redirect()
            ->route('central.laboratorios.index')
            ->with('success', 'Información del fabricante actualizada.');
    }
 

    public function destroy(Laboratorio $laboratorio)
    {
        $this->authorize('maestros.delete');


        if ($laboratorio->productos()->exists()) {
            return back()->with('error', 'Acción bloqueada: No se puede eliminar un laboratorio que posee productos vinculados en el catálogo.');
        }
 
        $laboratorio->delete();
 
        return redirect()
            ->route('central.laboratorios.index')
            ->with('success', 'El registro del laboratorio ha sido eliminado.');
    }
}