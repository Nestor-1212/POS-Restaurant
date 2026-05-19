@extends('layouts.app')
@section('title', 'Mesas')
@section('page-title', 'Mesas')
@section('page-subtitle', 'Control de mesas del restaurante')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-400 text-sm">{{ $mesas->count() }} mesas configuradas</p>
    <a href="{{ route('mesas.create') }}" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i> Nueva Mesa
    </a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
    @forelse($mesas as $mesa)
    @php
        $colors = match($mesa->estado) {
            'disponible' => ['border-green-500/40', 'text-green-400', 'bg-green-500/10'],
            'ocupada' => ['border-red-500/40', 'text-red-400', 'bg-red-500/10'],
            'reservada' => ['border-yellow-500/40', 'text-yellow-400', 'bg-yellow-500/10'],
            default => ['border-gray-700', 'text-gray-500', 'bg-gray-800'],
        };
    @endphp
    <div class="card border-2 {{ $colors[0] }} rounded-xl p-4 text-center">
        <div class="w-12 h-12 mx-auto mb-3 {{ $colors[2] }} rounded-xl flex items-center justify-center">
            <i class="fas fa-chair text-2xl {{ $colors[1] }}"></i>
        </div>
        <p class="font-bold text-white">{{ $mesa->nombre }}</p>
        <p class="text-xs {{ $colors[1] }} mt-1 capitalize">{{ $mesa->estado }}</p>
        <p class="text-xs text-gray-500">Cap. {{ $mesa->capacidad }}</p>
        @if($mesa->ubicacion)
        <p class="text-xs text-gray-500">{{ $mesa->ubicacion }}</p>
        @endif
        <div class="flex gap-1 mt-3">
            <a href="{{ route('mesas.edit', $mesa) }}" class="flex-1 text-center bg-gray-700 hover:bg-gray-600 text-white text-xs py-1 rounded-lg transition-colors">
                <i class="fas fa-pen-to-square"></i>
            </a>
            <form action="{{ route('mesas.destroy', $mesa) }}" method="POST" onsubmit="return confirm('¿Eliminar esta mesa?')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-gray-700 hover:bg-red-500/20 text-red-400 text-xs px-2 py-1 rounded-lg transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-gray-500">
        <i class="fas fa-chair text-4xl mb-3 block"></i>
        <p>No hay mesas registradas</p>
    </div>
    @endforelse
</div>
@endsection
