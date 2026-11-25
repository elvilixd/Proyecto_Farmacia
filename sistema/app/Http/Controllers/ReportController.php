<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Prepara los datos utilizados por los gráficos de reportes.
    public function index()
    {
        // Agrupa las ventas por categoría mediante el detalle de cada venta.
        $salesByCategory = DB::table('detailsales')
            ->join('medicines', 'medicines.id', '=', 'detailsales.medicines_id')
            ->join('categories', 'categories.id', '=', 'medicines.category_id')
            ->select('categories.name', DB::raw('SUM(detailsales.subtotal) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        // Construye una serie diaria de los últimos siete días.
        $dailySales = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo)->startOfDay();

            return (object) [
                'label' => $date->format('d/m'),
                'total' => (float) DB::table('sales')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total'),
            ];
        });

        // Obtiene los cinco medicamentos con mayor cantidad vendida.
        $topMedicines = DB::table('detailsales')
            ->join('medicines', 'medicines.id', '=', 'detailsales.medicines_id')
            ->select('medicines.name', DB::raw('SUM(detailsales.amount) as amount'))
            ->groupBy('medicines.name')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();

        return view('principal.reports.index', compact(
            'salesByCategory',
            'dailySales',
            'topMedicines'
        ));
    }
}
