<?php

namespace App\Http\Controllers;

class MedicineController extends Controller
{
    // Muestra la sección base de medicamentos.
    public function index()
    {
        return view('management.medicines.index');
    }
}
