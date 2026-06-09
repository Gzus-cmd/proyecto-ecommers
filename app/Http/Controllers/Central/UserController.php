<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Solo el Admin puede ver esto, lo bloquearemos también en rutas
        $query = User::with(['roles', 'sede']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Central/Usuarios/Index', [
            'usuarios' => $query->paginate(15)->withQueryString(),
            'filters'  => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Central/Usuarios/Create', [
            'sedes' => Sede::where('activo', true)->get(['id', 'nombre']),
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'sede_id'  => 'nullable|exists:central_sedes,id',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'sede_id'  => $validated['sede_id'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('central.usuarios.index')
            ->with('success', 'Usuario técnico creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return Inertia::render('Central/Usuarios/Edit', [
            'usuario' => $usuario->load('roles'),
            'sedes'   => Sede::where('activo', true)->get(['id', 'nombre']),
            'roles'   => Role::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|string|email|max:255|unique:users,email,{$usuario->id}",
            'sede_id'  => 'nullable|exists:central_sedes,id',
            'role'     => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $usuario->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'sede_id' => $validated['sede_id'],
        ]);

        if ($request->filled('password')) {
            $usuario->update(['password' => Hash::make($validated['password'])]);
        }

        $usuario->syncRoles($validated['role']);

        return redirect()->route('central.usuarios.index')
            ->with('success', 'Credenciales actualizadas.');
    }

public function destroy(User $usuario)
{

    if ($usuario->id === Auth::id()) {
        return back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
    }

    $usuario->delete();

    return redirect()->route('central.usuarios.index')
        ->with('success', 'Usuario removido del sistema.');
}
}