<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use Illuminate\Http\Request;
use Inertia\Inertia;
 
class LaboratorioController extends Controller
{
    public function index(Request $request)
    {
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
        return Inertia::render('Central/Laboratorios/Create');
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:central_laboratorios,nombre',
            'pais'   => 'nullable|string|max:100',
        ]);
 
        Laboratorio::create($validated);
 
        return redirect()
            ->route('central.laboratorios.index')
            ->with('success', 'Laboratorio creado correctamente.');
    }
 
    public function edit(Laboratorio $laboratorio)
    {
        return Inertia::render('Central/Laboratorios/Edit', [
            'laboratorio' => $laboratorio,
        ]);
    }
 
    public function update(Request $request, Laboratorio $laboratorio)
    {
        $validated = $request->validate([
            'nombre' => "required|string|max:255|unique:central_laboratorios,nombre,{$laboratorio->id}",
            'pais'   => 'nullable|string|max:100',
        ]);
 
        $laboratorio->update($validated);
 
        return redirect()
            ->route('central.laboratorios.index')
            ->with('success', 'Laboratorio actualizado correctamente.');
    }
 
    public function destroy(Laboratorio $laboratorio)
    {
        if ($laboratorio->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el laboratorio tiene productos asociados.');
        }
 
        $laboratorio->delete();
 
        return redirect()
            ->route('central.laboratorios.index')
            ->with('success', 'Laboratorio eliminado correctamente.');
    }
}