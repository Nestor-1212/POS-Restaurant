<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $mesas = Mesa::where('estado', '!=', 'inactiva')->orderBy('nombre')->get();
        return view('pos.index', compact('mesas'));
    }

    public function orden(Mesa $mesa = null)
    {
        $categorias = Categoria::where('activo', true)
            ->with(['productosActivos' => function ($q) {
                $q->with('variantes');
            }])
            ->orderBy('orden')
            ->get();

        $mesas = Mesa::where('estado', '!=', 'inactiva')->orderBy('nombre')->get();

        $ventaActiva = null;
        if ($mesa) {
            $ventaActiva = $mesa->ventaActiva()->with('detalles.producto', 'detalles.variante')->first();
        }

        return view('pos.orden', compact('categorias', 'mesas', 'mesa', 'ventaActiva'));
    }

    public function agregarProducto(Request $request)
    {
        $request->validate([
            'mesa_id' => 'nullable|exists:mesas,id',
            'producto_id' => 'required|exists:productos,id',
            'variante_id' => 'nullable|exists:variantes_producto,id',
            'cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $producto = Producto::findOrFail($request->producto_id);
            $precio = $request->variante_id
                ? $producto->variantes()->findOrFail($request->variante_id)->precio
                : $producto->precio;

            $venta = $this->obtenerOCrearVenta($request->mesa_id);

            $detalle = $venta->detalles()
                ->where('producto_id', $request->producto_id)
                ->where('variante_id', $request->variante_id)
                ->first();

            if ($detalle) {
                $detalle->increment('cantidad', $request->cantidad);
                $detalle->update(['subtotal' => $detalle->precio_unitario * $detalle->cantidad]);
            } else {
                $venta->detalles()->create([
                    'producto_id' => $request->producto_id,
                    'variante_id' => $request->variante_id,
                    'cantidad' => $request->cantidad,
                    'precio_unitario' => $precio,
                    'descuento' => 0,
                    'subtotal' => $precio * $request->cantidad,
                    'notas' => $request->notas,
                ]);
            }

            $this->recalcularVenta($venta);
        });

        return response()->json(['success' => true]);
    }

    public function quitarProducto(Request $request, DetalleVenta $detalle)
    {
        $venta = $detalle->venta;
        $detalle->delete();
        $this->recalcularVenta($venta);

        if ($venta->detalles()->count() === 0) {
            if ($venta->mesa) {
                $venta->mesa->update(['estado' => 'disponible']);
            }
            $venta->delete();
        }

        return response()->json(['success' => true]);
    }

    public function actualizarCantidad(Request $request, DetalleVenta $detalle)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);

        $detalle->update([
            'cantidad' => $request->cantidad,
            'subtotal' => $detalle->precio_unitario * $request->cantidad,
        ]);
        $this->recalcularVenta($detalle->venta);

        return response()->json(['success' => true]);
    }

    public function cobrar(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'metodo_pago' => 'required|in:efectivo,tarjeta,yappy,ach,transferencia,mixto',
            'monto_recibido' => 'nullable|numeric|min:0',
            'cliente_nombre' => 'nullable|string|max:150',
            'cliente_ruc' => 'nullable|string|max:30',
            'detalle_pago' => 'nullable|array',
        ]);

        $venta = Venta::with('detalles.producto')->findOrFail($request->venta_id);

        DB::transaction(function () use ($request, $venta) {
            $montoRecibido = $request->monto_recibido ?? $venta->total;
            $cambio = max(0, $montoRecibido - $venta->total);

            $venta->update([
                'metodo_pago' => $request->metodo_pago,
                'monto_recibido' => $montoRecibido,
                'cambio' => $cambio,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_ruc' => $request->cliente_ruc,
                'detalle_pago' => $request->detalle_pago,
                'estado' => 'completada',
                'completada_at' => now(),
            ]);

            if ($venta->mesa) {
                $venta->mesa->update(['estado' => 'disponible']);
            }

            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $stockAnterior = $producto->stock;
                $stockNuevo = max(0, $stockAnterior - $detalle->cantidad);
                $producto->update(['stock' => $stockNuevo]);

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'usuario_id' => auth()->id(),
                    'venta_id' => $venta->id,
                    'tipo' => 'salida',
                    'cantidad' => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => 'Venta ' . $venta->numero_factura,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'venta_id' => $venta->id,
            'redirect' => route('pos.factura', $venta->id),
        ]);
    }

    public function factura(Venta $venta)
    {
        $venta->load('detalles.producto', 'detalles.variante', 'mesa', 'usuario');
        return view('pos.factura', compact('venta'));
    }

    public function obtenerVenta($mesaId = null)
    {
        $venta = $mesaId
            ? Venta::where('mesa_id', $mesaId)->whereIn('estado', ['pendiente', 'en_preparacion'])->with('detalles.producto', 'detalles.variante')->first()
            : null;

        return response()->json(['venta' => $venta]);
    }

    private function obtenerOCrearVenta($mesaId = null): Venta
    {
        if ($mesaId) {
            $venta = Venta::where('mesa_id', $mesaId)
                ->whereIn('estado', ['pendiente', 'en_preparacion'])
                ->first();

            if ($venta) return $venta;

            Mesa::find($mesaId)?->update(['estado' => 'ocupada']);
        }

        return Venta::create([
            'numero_factura' => Venta::generarNumeroFactura(),
            'mesa_id' => $mesaId,
            'usuario_id' => auth()->id(),
            'subtotal' => 0,
            'descuento' => 0,
            'impuesto' => 0,
            'total' => 0,
            'estado' => 'pendiente',
        ]);
    }

    private function recalcularVenta(Venta $venta): void
    {
        $venta->refresh();
        $subtotal = $venta->detalles->sum('subtotal');
        $descuento = $venta->descuento ?? 0;
        $impuesto = ($subtotal - $descuento) * 0.07;
        $total = $subtotal - $descuento + $impuesto;

        $venta->update([
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total,
        ]);
    }
}
