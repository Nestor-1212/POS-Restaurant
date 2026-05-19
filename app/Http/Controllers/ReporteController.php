<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\CierreCaja;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->startOfMonth()->toDateString();
        $fechaFin = $request->fecha_fin ?? now()->toDateString();

        $ventas = Venta::where('estado', 'completada')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->with('usuario', 'mesa')
            ->orderByDesc('created_at')
            ->paginate(20);

        $resumen = Venta::where('estado', 'completada')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->select(
                DB::raw('COUNT(*) as num_ventas'),
                DB::raw('SUM(subtotal) as total_subtotal'),
                DB::raw('SUM(descuento) as total_descuento'),
                DB::raw('SUM(impuesto) as total_impuesto'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->first();

        $porMetodo = Venta::where('estado', 'completada')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->select('metodo_pago', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(total) as total'))
            ->groupBy('metodo_pago')
            ->get();

        return view('reportes.ventas', compact('ventas', 'resumen', 'porMetodo', 'fechaInicio', 'fechaFin'));
    }

    public function productos(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->startOfMonth()->toDateString();
        $fechaFin = $request->fecha_fin ?? now()->toDateString();

        $productos = DetalleVenta::whereHas('venta', function ($q) use ($fechaInicio, $fechaFin) {
                $q->where('estado', 'completada')
                  ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin]);
            })
            ->select(
                'producto_id',
                DB::raw('SUM(cantidad) as total_vendido'),
                DB::raw('SUM(subtotal) as total_ingresos')
            )
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->with('producto.categoria')
            ->paginate(20);

        return view('reportes.productos', compact('productos', 'fechaInicio', 'fechaFin'));
    }

    public function cierresCaja(Request $request)
    {
        $cierres = CierreCaja::with('usuario')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reportes.cierres_caja', compact('cierres'));
    }

    public function historialFacturas(Request $request)
    {
        $query = Venta::with('usuario', 'mesa');

        if ($request->search) {
            $query->where('numero_factura', 'like', '%' . $request->search . '%')
                  ->orWhere('cliente_nombre', 'like', '%' . $request->search . '%');
        }
        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $ventas = $query->orderByDesc('created_at')->paginate(20);

        return view('reportes.historial', compact('ventas'));
    }

    public function stockBajo()
    {
        $productos = Producto::where('activo', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->with('categoria')
            ->orderBy('stock')
            ->get();

        return view('reportes.stock_bajo', compact('productos'));
    }

    public function abrirCaja(Request $request)
    {
        $request->validate(['saldo_inicial' => 'required|numeric|min:0']);

        CierreCaja::create([
            'usuario_id' => auth()->id(),
            'saldo_inicial' => $request->saldo_inicial,
            'apertura_at' => now(),
        ]);

        return back()->with('success', 'Caja abierta.');
    }

    public function cerrarCaja(Request $request, CierreCaja $cierreCaja)
    {
        $request->validate(['observaciones' => 'nullable|string']);

        $ventas = Venta::where('estado', 'completada')
            ->where('created_at', '>=', $cierreCaja->apertura_at)
            ->get();

        $cierreCaja->update([
            'total_efectivo' => $ventas->where('metodo_pago', 'efectivo')->sum('total'),
            'total_tarjeta' => $ventas->where('metodo_pago', 'tarjeta')->sum('total'),
            'total_yappy' => $ventas->where('metodo_pago', 'yappy')->sum('total'),
            'total_otros' => $ventas->whereIn('metodo_pago', ['ach', 'transferencia', 'mixto'])->sum('total'),
            'total_ventas' => $ventas->sum('total'),
            'num_ventas' => $ventas->count(),
            'saldo_final' => $cierreCaja->saldo_inicial + $ventas->where('metodo_pago', 'efectivo')->sum('total'),
            'observaciones' => $request->observaciones,
            'cierre_at' => now(),
        ]);

        return back()->with('success', 'Caja cerrada exitosamente.');
    }
}
