<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
 
class CategoriaController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Categoria::orderBy('nombre');
 
        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%");
        }
 
        $categorias = $query->paginate(15)->withQueryString();
 
        return Inertia::render('Central/Categorias/Index', [
            'categorias' => $categorias,
            'filters'    => $request->only(['search']),
        ]);
    }
 
    // ── CREATE ───────────────────────────────────────────────
    public function create()
    {
        return Inertia::render('Central/Categorias/Create');
    }
 
    // ── STORE ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:central_categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
        ]);
 
        Categoria::create($validated);
 
        return redirect()
            ->route('central.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }
 
    // ── EDIT ─────────────────────────────────────────────────
    public function edit(Categoria $categoria)
    {
        return Inertia::render('Central/Categorias/Edit', [
            'categoria' => $categoria,
        ]);
    }
 
    // ── UPDATE ───────────────────────────────────────────────
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre'      => "required|string|max:255|unique:central_categorias,nombre,{$categoria->id}",
            'descripcion' => 'nullable|string|max:500',
        ]);
 
        $categoria->update($validated);
 
        return redirect()
            ->route('central.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }
 
    // ── DESTROY ──────────────────────────────────────────────
    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene productos asociados.');
        }
 
        $categoria->delete();
 
        return redirect()
            ->route('central.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}