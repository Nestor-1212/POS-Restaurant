@extends('layouts.app')
@section('title', 'Punto de Venta')
@section('page-title', 'Punto de Venta')
@section('page-subtitle', 'Selecciona una mesa o inicia venta directa')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-white font-semibold">Mesas del Restaurante</h2>
    <a href="{{ route('pos.orden') }}" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors">
        <i class="fas fa-shopping-bag"></i> Venta Directa (Sin Mesa)
    </a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
    @foreach($mesas as $mesa)
    @php
        $colorBg = match($mesa->estado) {
            'disponible' => 'bg-green-500/10 border-green-500/30 hover:border-green-400',
            'ocupada' => 'bg-red-500/10 border-red-500/30 hover:border-red-400',
            'reservada' => 'bg-yellow-500/10 border-yellow-500/30 hover:border-yellow-400',
            default => 'bg-gray-800 border-gray-700 opacity-50 cursor-not-allowed',
        };
        $iconColor = match($mesa->estado) {
            'disponible' => 'text-green-400',
            'ocupada' => 'text-red-400',
            'reservada' => 'text-yellow-400',
            default => 'text-gray-500',
        };
        $statusLabel = match($mesa->estado) {
            'disponible' => 'Disponible',
            'ocupada' => 'Ocupada',
            'reservada' => 'Reservada',
            default => 'Inactiva',
        };
    @endphp
    @if($mesa->estado !== 'inactiva')
    <a href="{{ route('pos.orden', $mesa) }}" class="border rounded-xl p-4 text-center transition-all {{ $colorBg }}">
    @else
    <div class="border rounded-xl p-4 text-center {{ $colorBg }}">
    @endif
        <div class="w-14 h-14 mx-auto mb-3 rounded-xl flex items-center justify-center bg-current/10">
            <i class="fas fa-chair text-2xl {{ $iconColor }}"></i>
        </div>
        <p class="font-bold text-white text-sm">{{ $mesa->nombre }}</p>
        <p class="text-xs {{ $iconColor }} mt-1">{{ $statusLabel }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Cap. {{ $mesa->capacidad }}</p>
        @if($mesa->estado === 'ocupada')
        <div class="mt-2 bg-red-500/20 rounded-full px-2 py-0.5">
            <p class="text-xs text-red-400"><i class="fas fa-circle-dot text-xs animate-pulse mr-1"></i>En curso</p>
        </div>
        @endif
    @if($mesa->estado !== 'inactiva')
    </a>
    @else
    </div>
    @endif
    @endforeach
</div>
@endsection
