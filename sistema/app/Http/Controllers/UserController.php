<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|size:8|unique:users,dni',
            'phone' => 'required|string|size:9',
            'tuition' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'dni' => $request->dni,
            'phone' => $request->phone,
            'tuition' => $request->tuition,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => ['required', 'string', 'size:8', Rule::unique('users', 'dni')->ignore($user->id)],
            'phone' => 'required|string|size:9',
            'tuition' => 'nullable|string|max:50',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = $request->only([
            'name', 'dni', 'phone', 'tuition', 'email', 'role_id'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
