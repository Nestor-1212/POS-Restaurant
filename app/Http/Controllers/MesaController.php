<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::withCount(['ventas as ventas_activas_count' => function ($q) {
            $q->whereIn('estado', ['pendiente', 'en_preparacion', 'listo']);
        }])->orderBy('nombre')->get();

        return view('mesas.index', compact('mesas'));
    }

    public function create()
    {
        return view('mesas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:mesas',
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'nullable|string|max:100',
        ]);

        Mesa::create($request->only(['nombre', 'capacidad', 'ubicacion']));

        return redirect()->route('mesas.index')->with('success', 'Mesa creada exitosamente.');
    }

    public function edit(Mesa $mesa)
    {
        return view('mesas.edit', compact('mesa'));
    }

    public function update(Request $request, Mesa $mesa)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:mesas,nombre,' . $mesa->id,
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'nullable|string|max:100',
            'estado' => 'required|in:disponible,ocupada,reservada,inactiva',
        ]);

        $mesa->update($request->only(['nombre', 'capacidad', 'ubicacion', 'estado']));

        return redirect()->route('mesas.index')->with('success', 'Mesa actualizada.');
    }

    public function destroy(Mesa $mesa)
    {
        if ($mesa->ventaActiva) {
            return back()->with('error', 'No se puede eliminar una mesa con pedido activo.');
        }
        $mesa->delete();
        return redirect()->route('mesas.index')->with('success', 'Mesa eliminada.');
    }
}
