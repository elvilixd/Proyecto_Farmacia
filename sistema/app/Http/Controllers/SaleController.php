<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // Muestra la sección base de ventas.
    public function index()
    {
        $sales = DB::table('sales')
            ->leftJoin('users', 'users.id', '=', 'sales.user_id')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->select('sales.*', 'users.name as user_name', 'customers.nombre as customer_name')
            ->orderByDesc('sales.id')
            ->get();

        return view('management.sales.index', compact('sales'));
    }

    // Muestra el formulario de venta sin permitir elegir al vendedor.
    public function create()
    {
        $customers = Customer::all();
        $medicines = Medicina::all();

        return view('management.sales.create', compact('customers', 'medicines'));
    }

    // Registra la venta asignando al usuario autenticado como vendedor.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'medicine_id' => ['required', 'exists:medicines,id'],
            'amount' => ['required', 'integer', 'min:1'],
        ]);

        $medicine = Medicina::findOrFail($validated['medicine_id']);
        $subtotal = $medicine->price * $validated['amount'];

        $saleId = DB::table('sales')->insertGetId([
            'customer_id' => $validated['customer_id'],
            'user_id' => auth()->id(),
            'date' => (float) now()->format('Ymd'),
            'total' => $subtotal,
            'igv' => $subtotal * 0.18,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('detailsales')->insert([
            'sale_id' => $saleId,
            'medicines_id' => $medicine->id,
            'amount' => $validated['amount'],
            'price_unit' => $medicine->price,
            'subtotal' => $subtotal,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('sales.index')
            ->with('ok', 'Venta registrada correctamente.');
    }

    // Anula una venta solo si la acción corresponde a un Administrador.
    public function destroy(int $sale)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }

        DB::table('sales')->where('id', $sale)->delete();

        return to_route('sales.index')
            ->with('ok', 'Venta anulada correctamente.');
    }
}
