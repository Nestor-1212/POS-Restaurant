@extends('layouts.app')
@section('title', 'Stock Bajo')
@section('page-title', 'Alertas de Stock Bajo')
@section('page-subtitle', 'Productos que requieren reabastecimiento')

@section('content')
@if($productos->count() === 0)
<div class="text-center py-16">
    <i class="fas fa-check-circle text-5xl text-green-400/40 mb-4 block"></i>
    <p class="text-green-400 font-medium">¡Inventario en buen estado!</p>
    <p class="text-gray-500 text-sm">No hay productos con stock bajo</p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($productos as $producto)
    <div class="card border border-red-500/30 rounded-xl p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box text-red-400"></i>
            </div>
            <div>
                <p class="font-medium text-white">{{ $producto->nombre }}</p>
                <p class="text-xs text-gray-400">{{ $producto->categoria->nombre }}</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center text-xs">
            <div class="bg-red-500/10 rounded-lg p-2">
                <p class="text-red-400 font-bold text-xl">{{ $producto->stock }}</p>
                <p class="text-gray-500">Stock actual</p>
            </div>
            <div class="bg-gray-800 rounded-lg p-2">
                <p class="text-white font-bold text-xl">{{ $producto->stock_minimo }}</p>
                <p class="text-gray-500">Mínimo</p>
            </div>
            <div class="bg-gray-800 rounded-lg p-2">
                <p class="text-primary font-bold text-xl">{{ max(0, $producto->stock_minimo * 3 - $producto->stock) }}</p>
                <p class="text-gray-500">A pedir</p>
            </div>
        </div>
        <form action="{{ route('productos.ajuste-stock', $producto) }}" method="POST" class="mt-3 flex gap-2">
            @csrf
            <input type="hidden" name="tipo" value="entrada">
            <input type="number" name="cantidad" min="1" placeholder="Cant." required
                class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-1.5 text-sm focus:outline-none">
            <button type="submit" class="bg-green-500 hover:bg-green-400 text-white px-3 py-1.5 rounded-lg text-sm transition-colors">
                <i class="fas fa-plus"></i>
            </button>
        </form>
    </div>
    @endforeach
</div>
@endif
@endsection
