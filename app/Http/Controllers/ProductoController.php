<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->search) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $productos = $query->orderBy('nombre')->paginate(15);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'impuesto' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
            'codigo_barra' => 'nullable|string|max:50|unique:productos',
            'tiempo_preparacion' => 'nullable|integer|min:0',
            'variantes' => 'nullable|array',
            'variantes.*.nombre' => 'required_with:variantes|string|max:50',
            'variantes.*.precio' => 'required_with:variantes|numeric|min:0',
        ]);

        $data = $request->only([
            'categoria_id', 'nombre', 'descripcion', 'precio', 'impuesto',
            'stock', 'stock_minimo', 'codigo_barra', 'tiempo_preparacion',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['tiene_variantes'] = $request->has('variantes') && count($request->variantes) > 0;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($data);

        if ($request->has('variantes')) {
            foreach ($request->variantes as $variante) {
                if (!empty($variante['nombre'])) {
                    $producto->variantes()->create([
                        'nombre' => $variante['nombre'],
                        'precio' => $variante['precio'],
                        'stock' => $variante['stock'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $producto->load('variantes');
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'impuesto' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'codigo_barra' => 'nullable|string|max:50|unique:productos,codigo_barra,' . $producto->id,
        ]);

        $data = $request->only([
            'categoria_id', 'nombre', 'descripcion', 'precio', 'impuesto',
            'stock', 'stock_minimo', 'codigo_barra', 'tiempo_preparacion',
        ]);
        $data['activo'] = $request->boolean('activo');

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        if ($request->has('variantes')) {
            $producto->variantes()->delete();
            foreach ($request->variantes as $variante) {
                if (!empty($variante['nombre'])) {
                    $producto->variantes()->create([
                        'nombre' => $variante['nombre'],
                        'precio' => $variante['precio'],
                        'stock' => $variante['stock'] ?? 0,
                    ]);
                }
            }
            $producto->update(['tiene_variantes' => count($request->variantes) > 0]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    public function toggleEstado(Producto $producto)
    {
        $producto->update(['activo' => !$producto->activo]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function inventario()
    {
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->orderBy('nombre')
            ->paginate(20);
        return view('productos.inventario', compact('productos'));
    }

    public function ajusteStock(Request $request, Producto $producto)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ]);

        $stockAnterior = $producto->stock;
        $nuevaCantidad = match($request->tipo) {
            'entrada' => $stockAnterior + $request->cantidad,
            'salida'  => max(0, $stockAnterior - $request->cantidad),
            'ajuste'  => $request->cantidad,
        };

        $producto->update(['stock' => $nuevaCantidad]);

        $producto->movimientosInventario()->create([
            'usuario_id' => auth()->id(),
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $nuevaCantidad,
            'motivo' => $request->motivo,
        ]);

        return back()->with('success', 'Stock actualizado.');
    }
}
