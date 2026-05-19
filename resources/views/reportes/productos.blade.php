@extends('layouts.app')
@section('title', 'Productos Vendidos')
@section('page-title', 'Productos Más Vendidos')

@section('content')
<div class="card rounded-xl p-4 mb-6">
    <form action="{{ route('reportes.productos') }}" method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-400 mb-1">Desde</label>
            <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Hasta</label>
            <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
        </div>
        <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">Filtrar</button>
    </form>
</div>

<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3">#</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Producto</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Categoría</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Unidades</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Ingresos</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($productos as $i => $item)
            <tr class="hover:bg-gray-800/30 transition-colors">
                <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                <td class="px-4 py-3 text-white font-medium">{{ $item->producto->nombre }}</td>
                <td class="px-4 py-3 text-gray-400">{{ $item->producto->categoria->nombre }}</td>
                <td class="px-4 py-3 text-right">
                    <span class="font-bold text-white">{{ $item->total_vendido }}</span>
                </td>
                <td class="px-4 py-3 text-right font-bold text-green-400">${{ number_format($item->total_ingresos, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-500">Sin datos en este período</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">{{ $productos->links() }}</div>
</div>
@endsection
