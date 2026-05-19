@extends('layouts.app')
@section('title', 'Reporte de Ventas')
@section('page-title', 'Reporte de Ventas')

@section('content')
{{-- Date Filter --}}
<div class="card rounded-xl p-4 mb-6">
    <form action="{{ route('reportes.ventas') }}" method="GET" class="flex flex-wrap gap-3 items-end">
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
        <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
            <i class="fas fa-filter mr-1"></i> Filtrar
        </button>
    </form>
</div>

{{-- Summary Cards --}}
@if($resumen)
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card rounded-xl p-4">
        <p class="text-xs text-gray-400">Total Ventas</p>
        <p class="text-2xl font-bold text-green-400">${{ number_format($resumen->total_ventas ?? 0, 2) }}</p>
    </div>
    <div class="stat-card rounded-xl p-4">
        <p class="text-xs text-gray-400">N° Ventas</p>
        <p class="text-2xl font-bold text-white">{{ $resumen->num_ventas ?? 0 }}</p>
    </div>
    <div class="stat-card rounded-xl p-4">
        <p class="text-xs text-gray-400">Total Impuesto</p>
        <p class="text-2xl font-bold text-yellow-400">${{ number_format($resumen->total_impuesto ?? 0, 2) }}</p>
    </div>
    <div class="stat-card rounded-xl p-4">
        <p class="text-xs text-gray-400">Total Descuentos</p>
        <p class="text-2xl font-bold text-red-400">${{ number_format($resumen->total_descuento ?? 0, 2) }}</p>
    </div>
</div>

{{-- By Payment Method --}}
<div class="card rounded-xl p-4 mb-6">
    <h3 class="font-semibold text-white mb-3 text-sm">Por Método de Pago</h3>
    <div class="flex flex-wrap gap-3">
        @foreach($porMetodo as $m)
        <div class="bg-gray-800 rounded-lg px-4 py-3 flex items-center gap-3">
            <i class="fas fa-{{ match($m->metodo_pago) { 'efectivo' => 'money-bill', 'tarjeta' => 'credit-card', 'yappy' => 'mobile-screen', default => 'circle' } }} text-primary"></i>
            <div>
                <p class="text-xs text-gray-400 capitalize">{{ $m->metodo_pago }}</p>
                <p class="text-white font-bold text-sm">${{ number_format($m->total, 2) }}</p>
                <p class="text-xs text-gray-500">{{ $m->cantidad }} ventas</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Ventas Table --}}
<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Factura</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Fecha</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Mesa</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Cajero</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Pago</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($ventas as $venta)
            <tr class="hover:bg-gray-800/30 transition-colors">
                <td class="px-4 py-3 text-primary font-medium">{{ $venta->numero_factura }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-gray-300">{{ $venta->mesa?->nombre ?? 'Directa' }}</td>
                <td class="px-4 py-3 text-gray-300">{{ $venta->usuario->name }}</td>
                <td class="px-4 py-3"><span class="text-xs capitalize text-gray-400">{{ $venta->metodo_pago }}</span></td>
                <td class="px-4 py-3 text-right font-bold text-white">${{ number_format($venta->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-gray-500">No hay ventas en este período</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">{{ $ventas->links() }}</div>
</div>
@endsection
