<?php
 
namespace App\Http\Controllers\Central;
 
use App\Http\Controllers\Controller;
use App\Models\ProductoMaestro;
use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class ProductoMaestroController extends Controller
{
    use AuthorizesRequests; 


    public function index(Request $request)
    {

        $this->authorize('inventario.view');

        $query = ProductoMaestro::with(['categoria', 'laboratorio', 'proveedor'])
            ->orderBy('nombre_comercial');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_comercial', 'like', "%{$search}%")
                  ->orWhere('nombre_generico', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }
 
        $productos = $query->paginate(15)->withQueryString();
 
        return Inertia::render('Central/Productos/Index', [
            'productos' => $productos,
            'filters'   => $request->only(['search', 'activo']),
        ]);
    }


    public function create()
    {
        $this->authorize('maestros.create');

        return Inertia::render('Central/Productos/Create', [
            'categorias'   => Categoria::orderBy('nombre')->get(['id', 'nombre']),
            'laboratorios' => Laboratorio::orderBy('nombre')->get(['id', 'nombre']),
            'proveedores'  => Proveedor::where('activo', true)->orderBy('razon_social')->get(['id', 'razon_social']),
        ]);
    }


    public function store(Request $request)
    {
        $this->authorize('maestros.create');

        $validated = $request->validate([
            'sku'                => 'required|string|max:100|unique:central_productos_maestro,sku',
            'nombre_comercial'   => 'required|string|max:255',
            'nombre_generico'    => 'nullable|string|max:255',
            'descripcion'        => 'nullable|string',
            'categoria_id'       => 'required|exists:central_categorias,id',
            'laboratorio_id'     => 'required|exists:central_laboratorios,id',
            'proveedor_id'       => 'required|exists:central_proveedores,id',
            'requiere_receta'    => 'boolean',
            'registro_sanitario' => 'nullable|string|max:100',
            'concentracion'      => 'nullable|string|max:100',
            'forma_farmaceutica' => 'nullable|string|max:100',
            'unidad_medida'      => 'nullable|string|max:50',
            'stock_minimo'       => 'integer|min:0',
            'activo'             => 'boolean',
        ]);
 
        ProductoMaestro::create($validated);
 
        return redirect()
            ->route('central.productos.index')
            ->with('success', 'Producto registrado en el catálogo maestro.');
    }


    public function show(ProductoMaestro $productoMaestro)
    {
        $this->authorize('inventario.view');
        $productoMaestro->load(['categoria', 'laboratorio', 'proveedor', 'lotes']);
 
        return Inertia::render('Central/Productos/Show', [
            'producto' => $productoMaestro,
        ]);
    }


    public function edit(ProductoMaestro $productoMaestro)
    {
        $this->authorize('maestros.update');

        return Inertia::render('Central/Productos/Edit', [
            'producto'     => $productoMaestro,
            'categorias'   => Categoria::orderBy('nombre')->get(['id', 'nombre']),
            'laboratorios' => Laboratorio::orderBy('nombre')->get(['id', 'nombre']),
            'proveedores'  => Proveedor::where('activo', true)->orderBy('razon_social')->get(['id', 'razon_social']),
        ]);
    }


    public function update(Request $request, ProductoMaestro $productoMaestro)
    {
        $this->authorize('maestros.update');

        $validated = $request->validate([
            'sku'                => "required|string|max:100|unique:central_productos_maestro,sku,{$productoMaestro->id}",
            'nombre_comercial'   => 'required|string|max:255',
            'nombre_generico'    => 'nullable|string|max:255',
            'descripcion'        => 'nullable|string',
            'categoria_id'       => 'required|exists:central_categorias,id',
            'laboratorio_id'     => 'required|exists:central_laboratorios,id',
            'proveedor_id'       => 'required|exists:central_proveedores,id',
            'requiere_receta'    => 'boolean',
            'registro_sanitario' => 'nullable|string|max:100',
            'concentracion'      => 'nullable|string|max:100',
            'forma_farmaceutica' => 'nullable|string|max:100',
            'unidad_medida'      => 'nullable|string|max:50',
            'stock_minimo'       => 'integer|min:0',
            'activo'             => 'boolean',
        ]);
 
        $productoMaestro->update($validated);
 
        return redirect()
            ->route('central.productos.index')
            ->with('success', 'Especificaciones de producto actualizadas.');
    }


    public function destroy(ProductoMaestro $productoMaestro)
    {
        $this->authorize('maestros.delete');

        if ($productoMaestro->lotes()->exists()) {
            return back()->with('error', 'Integridad del Kardex: No se puede eliminar un producto con lotes históricos.');
        }
 
        $productoMaestro->delete();
 
        return redirect()
            ->route('central.productos.index')
            ->with('success', 'Registro removido del catálogo maestro.');
    }
}