<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Muestra el listado de roles registrados.
    public function index()
    {
        $roles = Role::all();

        return view('settings.roles.index', compact('roles'));
    }

    // Muestra el formulario de registro de un rol.
    public function create()
    {
        return view('settings.roles.create');
    }

    // Guarda un nuevo rol en la base de datos.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Role::create($validated);

        return to_route('roles.index')
            ->with('ok', 'Rol registrado correctamente.');
    }

    // Muestra el formulario de edición de un rol.
    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    // Actualiza los datos de un rol existente.
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update($validated);

        return to_route('roles.index')
            ->with('ok', 'Rol actualizado correctamente.');
    }

    // Elimina un rol cuando no está relacionado con usuarios.
    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return to_route('roles.index')
                ->with('error', 'No se puede eliminar un rol asignado a usuarios.');
        }

        $role->delete();

        return to_route('roles.index')
            ->with('ok', 'Rol eliminado correctamente.');
    }
}
