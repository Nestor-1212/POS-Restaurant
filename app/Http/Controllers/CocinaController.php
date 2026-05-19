<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;

class CocinaController extends Controller
{
    public function index()
    {
        $pedidos = Venta::whereIn('estado', ['pendiente', 'en_preparacion'])
            ->with('detalles.producto', 'detalles.variante', 'mesa', 'usuario')
            ->orderBy('created_at')
            ->get();

        return view('cocina.index', compact('pedidos'));
    }

    public function marcarEnPreparacion(Venta $venta)
    {
        $venta->update(['estado' => 'en_preparacion']);
        $venta->detalles()->where('estado', 'pendiente')->update(['estado' => 'en_preparacion']);

        return response()->json(['success' => true, 'estado' => 'en_preparacion']);
    }

    public function marcarListo(Venta $venta)
    {
        $venta->update(['estado' => 'listo']);
        $venta->detalles()->update(['estado' => 'listo']);

        return response()->json(['success' => true, 'estado' => 'listo']);
    }

    public function marcarDetallePreparado(DetalleVenta $detalle)
    {
        $detalle->update(['estado' => 'listo']);

        $venta = $detalle->venta;
        $todosListos = $venta->detalles()->where('estado', '!=', 'listo')->doesntExist();

        if ($todosListos) {
            $venta->update(['estado' => 'listo']);
        }

        return response()->json(['success' => true, 'todos_listos' => $todosListos]);
    }

    public function pedidosJson()
    {
        $pedidos = Venta::whereIn('estado', ['pendiente', 'en_preparacion'])
            ->with('detalles.producto', 'mesa')
            ->orderBy('created_at')
            ->get();

        return response()->json($pedidos);
    }
}
