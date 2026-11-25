<?php

namespace App\Http\Controllers;

class CustomerController extends Controller
{
    // Muestra la sección base de clientes.
    public function index()
    {
        return view('management.customers.index');
    }
}
