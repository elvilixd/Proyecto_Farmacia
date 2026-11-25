<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra el formulario manual de inicio de sesión.
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Valida las credenciales y inicia la sesión del usuario.
    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $recordar = $request->boolean('remember');

        if (Auth::attempt($credenciales, $recordar)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.index'));
        }

        // Conserva únicamente el correo cuando las credenciales son incorrectas.
        return back()
            ->withErrors(['email' => 'El correo o la contraseña no son correctos.'])
            ->withInput($request->only('email'));
    }

    // Cierra la sesión actual y renueva el token de seguridad.
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('ok', 'Cerraste sesión correctamente.');
    }
}
