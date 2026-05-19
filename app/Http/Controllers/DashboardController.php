<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        $ventasHoy = Venta::whereDate('created_at', $hoy)
            ->where('estado', 'completada')
            ->sum('total');

        $numVentasHoy = Venta::whereDate('created_at', $hoy)
            ->where('estado', 'completada')
            ->count();

        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $totalMesas = Mesa::where('estado', '!=', 'inactiva')->count();

        $pedidosPendientes = Venta::whereIn('estado', ['pendiente', 'en_preparacion'])->count();

        $productosVendidosHoy = DetalleVenta::whereHas('venta', function ($q) use ($hoy) {
                $q->whereDate('created_at', $hoy)->where('estado', 'completada');
            })
            ->select('producto_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('producto_id')
            ->orderByDesc('total')
            ->with('producto')
            ->limit(5)
            ->get();

        $ventasUltimos7Dias = Venta::where('estado', 'completada')
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(total) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $alertasStock = Producto::where('activo', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->with('categoria')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'ventasHoy', 'numVentasHoy', 'mesasOcupadas', 'totalMesas',
            'pedidosPendientes', 'productosVendidosHoy', 'ventasUltimos7Dias', 'alertasStock'
        ));
    }
}
