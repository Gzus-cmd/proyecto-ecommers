<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;
 
class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::orderBy('razon_social');
 
        if ($request->filled('search')) {
            $query->where('razon_social', 'like', "%{$request->search}%")
                  ->orWhere('ruc', 'like', "%{$request->search}%")
                  ->orWhere('contacto', 'like', "%{$request->search}%");
        }
 
        if ($request->filled('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }
 
        $proveedores = $query->paginate(15)->withQueryString();
 
        return Inertia::render('Central/Proveedores/Index', [
            'proveedores' => $proveedores,
            'filters'     => $request->only(['search', 'activo']),
        ]);
    }
 
    public function create()
    {
        return Inertia::render('Central/Proveedores/Create');
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc'          => 'required|string|max:20|unique:central_proveedores,ruc',
            'contacto'     => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'direccion'    => 'nullable|string|max:500',
            'activo'       => 'boolean',
        ]);
 
        Proveedor::create($validated);
 
        return redirect()
            ->route('central.proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }
 

    public function edit(Proveedor $proveedor)
    {
    // Si llegaste aquí y el ID sigue siendo null, revisa que el Modelo Proveedor
    // tenga los campos en el array $fillable.
    
        return Inertia::render('Central/Proveedores/Edit', [
            'proveedor' => $proveedor
        ]);
    }
 
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc'          => "required|string|max:20|unique:central_proveedores,ruc,{$proveedor->id}",
            'contacto'     => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'direccion'    => 'nullable|string|max:500',
            'activo'       => 'boolean',
        ]);
 
        $proveedor->update($validated);
 
        return redirect()
            ->route('central.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }
 
    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el proveedor tiene productos asociados.');
        }
 
        $proveedor->delete();
 
        return redirect()
            ->route('central.proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }

    public function show(Proveedor $proveedor)
{
    return Inertia::render('Central/Proveedores/Show', [
        'proveedor' => [
            'id'           => $proveedor->id,
            'razon_social' => $proveedor->razon_social,
            'ruc'          => $proveedor->ruc,
            'contacto'     => $proveedor->contacto,
            'telefono'     => $proveedor->telefono,
            'email'        => $proveedor->email,
            'direccion'    => $proveedor->direccion,
            'activo'       => $proveedor->activo,
            'created_at'   => $proveedor->created_at?->format('d/m/Y H:i'),
            'updated_at'   => $proveedor->updated_at?->format('d/m/Y H:i'),
        ],
    ]);
}

}