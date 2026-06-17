<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class CategoriaController extends Controller
{
    use AuthorizesRequests; 


    public function index(Request $request)
    {
        $this->authorize('inventario.view');

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
 

    public function create()
    {
        $this->authorize('maestros.create');
        
        return Inertia::render('Central/Categorias/Create');
    }
 

    public function store(Request $request)
    {
        $this->authorize('maestros.create');

        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:central_categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
        ]);
 
        Categoria::create($validated);
 
        return redirect()
            ->route('central.categorias.index')
            ->with('success', 'Nueva categoría técnica registrada correctamente.');
    }
 

    public function edit(Categoria $categoria)
    {
        $this->authorize('maestros.update');

        return Inertia::render('Central/Categorias/Edit', [
            'categoria' => $categoria,
        ]);
    }
 

    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('maestros.update');

        $validated = $request->validate([
            'nombre'      => "required|string|max:255|unique:central_categorias,nombre,{$categoria->id}",
            'descripcion' => 'nullable|string|max:500',
        ]);
 
        $categoria->update($validated);
 
        return redirect()
            ->route('central.categorias.index')
            ->with('success', 'Categoría de producto actualizada.');
    }
 

    public function destroy(Categoria $categoria)
    {
        $this->authorize('maestros.delete');


        if ($categoria->productos()->exists()) {
            return back()->with('error', 'Restricción de integridad: Esta categoría contiene productos activos y no puede eliminarse.');
        }
 
        $categoria->delete();
 
        return redirect()
            ->route('central.categorias.index')
            ->with('success', 'La categoría ha sido purgada del sistema.');
    }
}