<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class ProveedorController extends Controller
{
    use AuthorizesRequests;


    public function index(Request $request)
    {
        $this->authorize('inventario.view');

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
        $this->authorize('maestros.create');

        return Inertia::render('Central/Proveedores/Create');
    }
 

    public function store(Request $request)
    {
        $this->authorize('maestros.create');

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
            ->with('success', 'Proveedor dado de alta en el sistema correctamente.');
    }


    public function show(Proveedor $proveedor)
    {
        $this->authorize('inventario.view');

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
 

    public function edit(Proveedor $proveedor)
    {
        $this->authorize('maestros.update');
    
        return Inertia::render('Central/Proveedores/Edit', [
            'proveedor' => $proveedor
        ]);
    }
 

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->authorize('maestros.update');

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
            ->with('success', 'Ficha de proveedor actualizada con éxito.');
    }
 

    public function destroy(Proveedor $proveedor)
    {
        $this->authorize('maestros.delete');


        if ($proveedor->productos()->exists()) {
            return back()->with('error', 'No es posible eliminar el proveedor: Existen productos vinculados a este registro en el catálogo maestro.');
        }
 
        $proveedor->delete();
 
        return redirect()
            ->route('central.proveedores.index')
            ->with('success', 'El proveedor ha sido removido del ecosistema.');
    }
}