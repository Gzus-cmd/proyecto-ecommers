<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class SedeController extends Controller
{
    use AuthorizesRequests; 


    public function index(Request $request)
    {
        $this->authorize('sedes.manage');

        $query = Sede::orderBy('nombre');

        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%")
                  ->orWhere('codigo', 'like', "%{$request->search}%");
        }

        $sedes = $query->paginate(15)->withQueryString();

        return Inertia::render('Central/Sedes/Index', [
            'sedes'   => $sedes,
            'filters' => $request->only(['search']),
        ]);
    }


    public function create()
    {
        $this->authorize('sedes.manage');

        return Inertia::render('Central/Sedes/Create');
    }


    public function store(Request $request)
    {
        $this->authorize('sedes.manage');

        $validated = $request->validate([
            'codigo'    => 'required|string|max:20|unique:central_sedes,codigo',
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono'  => 'nullable|string|max:50',
            'activo'    => 'required|boolean',
        ]);

        Sede::create($validated);

        return redirect()
            ->route('central.sedes.index')
            ->with('success', 'Sede operativa registrada exitosamente en el sistema.');
    }


    public function edit(Sede $sede)
    {
        $this->authorize('sedes.manage');

        return Inertia::render('Central/Sedes/Edit', [
            'sede' => $sede,
        ]);
    }


    public function update(Request $request, Sede $sede)
    {
        $this->authorize('sedes.manage');

        $validated = $request->validate([
            'codigo'    => "required|string|max:20|unique:central_sedes,codigo,{$sede->id}",
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono'  => 'nullable|string|max:50',
            'activo'    => 'required|boolean',
        ]);

        $sede->update($validated);

        return redirect()
            ->route('central.sedes.index')
            ->with('success', 'La información de la sede ha sido actualizada.');
    }


    public function destroy(Sede $sede)
    {
        $this->authorize('sedes.manage');

        
        $sede->delete();

        return redirect()
            ->route('central.sedes.index')
            ->with('success', 'Sede eliminada permanentemente del ecosistema.');
    }
}