<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SedeController extends Controller
{
    public function index(Request $request)
    {
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
        return Inertia::render('Central/Sedes/Create');
    }

    public function store(Request $request)
    {
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
            ->with('success', 'Sede creada correctamente.');
    }

    public function edit(Sede $sede)
    {
        return Inertia::render('Central/Sedes/Edit', [
            'sede' => $sede,
        ]);
    }

    public function update(Request $request, Sede $sede)
    {
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
            ->with('success', 'Sede actualizada correctamente.');
    }

    public function destroy(Sede $sede)
    {
        // Nota: Podrías añadir validación aquí si una sede tiene stock o transferencias asociadas
        $sede->delete();

        return redirect()
            ->route('central.sedes.index')
            ->with('success', 'Sede eliminada permanentemente.');
    }
}