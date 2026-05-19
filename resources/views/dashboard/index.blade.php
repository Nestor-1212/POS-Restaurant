@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen del día - {{ now()->format("d/m/Y") }}')

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-400">Ventas del Día</p>
            <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">${{ number_format($ventasHoy, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $numVentasHoy }} ventas completadas</p>
    </div>

    <div class="stat-card rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-400">Mesas Ocupadas</p>
            <div class="w-10 h-10 bg-orange-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-chair text-orange-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $mesasOcupadas }}<span class="text-lg text-gray-500">/{{ $totalMesas }}</span></p>
        <div class="mt-2 bg-gray-700 rounded-full h-1.5">
            <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $totalMesas > 0 ? ($mesasOcupadas/$totalMesas)*100 : 0 }}%"></div>
        </div>
    </div>

    <div class="stat-card rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-400">Pedidos Pendientes</p>
            <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-yellow-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pedidosPendientes }}</p>
        <p class="text-xs text-gray-500 mt-1">En cola o preparación</p>
    </div>

    <div class="stat-card rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-400">Alertas Stock</p>
            <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-triangle-exclamation text-red-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $alertasStock->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">Productos con stock bajo</p>
    </div>
</div>

{{-- Charts + Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Ventas últimos 7 días --}}
    <div class="lg:col-span-2 card rounded-xl p-5">
        <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-primary"></i> Ventas Últimos 7 Días
        </h3>
        <div class="flex items-end gap-2 h-36">
            @php
                $maxVenta = $ventasUltimos7Dias->max('total') ?: 1;
                $dias = collect();
                for ($i = 6; $i >= 0; $i--) {
                    $fecha = now()->subDays($i)->toDateString();
                    $venta = $ventasUltimos7Dias->firstWhere('fecha', $fecha);
                    $dias->push(['fecha' => $fecha, 'total' => $venta?->total ?? 0]);
                }
            @endphp
            @foreach($dias as $dia)
            <div class="flex-1 flex flex-col items-center gap-1">
                <p class="text-xs text-gray-500">${{ number_format($dia['total'], 0) }}</p>
                <div class="w-full bg-gray-700 rounded-t relative" style="height: {{ max(4, ($dia['total']/$maxVenta)*120) }}px">
                    <div class="absolute inset-0 bg-primary/70 rounded-t hover:bg-primary transition-colors"></div>
                </div>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($dia['fecha'])->format('D') }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Productos más vendidos --}}
    <div class="card rounded-xl p-5">
        <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-fire text-orange-400"></i> Más Vendidos Hoy
        </h3>
        @forelse($productosVendidosHoy as $item)
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-burger text-primary text-xs"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-white truncate">{{ $item->producto->nombre }}</p>
                <div class="w-full bg-gray-700 rounded-full h-1 mt-1">
                    <div class="bg-primary h-1 rounded-full" style="width: {{ ($item->total / ($productosVendidosHoy->max('total') ?: 1)) * 100 }}%"></div>
                </div>
            </div>
            <span class="text-sm font-bold text-primary">{{ $item->total }}</span>
        </div>
        @empty
        <p class="text-gray-500 text-sm text-center py-4">Sin ventas hoy</p>
        @endforelse
    </div>
</div>

{{-- Alertas Stock --}}
@if($alertasStock->count() > 0)
<div class="mt-6 card rounded-xl p-5">
    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-triangle-exclamation text-yellow-400"></i> Alertas de Inventario
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($alertasStock as $producto)
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg px-4 py-3 flex items-center gap-3">
            <i class="fas fa-box text-yellow-400"></i>
            <div>
                <p class="text-sm text-white font-medium">{{ $producto->nombre }}</p>
                <p class="text-xs text-yellow-400">Stock: {{ $producto->stock }} / Mín: {{ $producto->stock_minimo }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
