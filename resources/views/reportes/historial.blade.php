@extends('layouts.app')
@section('title', 'Historial de Facturas')
@section('page-title', 'Historial de Facturas')

@section('content')
<div class="flex gap-3 mb-6">
    <form action="{{ route('reportes.historial') }}" method="GET" class="flex gap-3 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por factura o cliente..."
            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
        <select name="estado" class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <option value="">Todos los estados</option>
            @foreach(['pendiente', 'en_preparacion', 'listo', 'completada', 'cancelada'] as $e)
            <option value="{{ $e }}" {{ request('estado') == $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">Buscar</button>
    </form>
</div>

<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Factura</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Fecha</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Cliente</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Cajero</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Estado</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Total</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($ventas as $venta)
            @php
                $estadoColors = ['pendiente' => 'bg-yellow-500/20 text-yellow-400', 'en_preparacion' => 'bg-orange-500/20 text-orange-400', 'listo' => 'bg-blue-500/20 text-blue-400', 'completada' => 'bg-green-500/20 text-green-400', 'cancelada' => 'bg-red-500/20 text-red-400'];
            @endphp
            <tr class="hover:bg-gray-800/30 transition-colors">
                <td class="px-4 py-3 text-primary font-medium">{{ $venta->numero_factura }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-gray-300">{{ $venta->cliente_nombre ?? 'Sin nombre' }}</td>
                <td class="px-4 py-3 text-gray-300">{{ $venta->usuario->name }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full {{ $estadoColors[$venta->estado] ?? 'bg-gray-700 text-gray-400' }}">
                        {{ ucfirst(str_replace('_', ' ', $venta->estado)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-bold text-white">${{ number_format($venta->total, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    @if($venta->estado === 'completada')
                    <a href="{{ route('pos.factura', $venta) }}" class="text-xs text-primary hover:underline">
                        <i class="fas fa-receipt mr-1"></i> Ver
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-8 text-gray-500">No hay facturas</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">{{ $ventas->links() }}</div>
</div>
@endsection
