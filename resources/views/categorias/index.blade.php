@extends('layouts.app')
@section('title', 'Categorías')
@section('page-title', 'Categorías')
@section('page-subtitle', 'Gestión de categorías de productos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-400 text-sm">{{ $categorias->count() }} categorías registradas</p>
    <a href="{{ route('categorias.create') }}" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i> Nueva Categoría
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($categorias as $categoria)
    <div class="card rounded-xl overflow-hidden">
        <div class="h-2" style="background: {{ $categoria->color ?? '#FF6B35' }}"></div>
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $categoria->color ?? '#FF6B35' }}22">
                    <i class="fas fa-{{ $categoria->icono ?? 'tag' }} text-lg" style="color: {{ $categoria->color ?? '#FF6B35' }}"></i>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $categoria->activo ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-500' }}">
                    {{ $categoria->activo ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
            <h3 class="font-semibold text-white">{{ $categoria->nombre }}</h3>
            @if($categoria->descripcion)
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $categoria->descripcion }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-2">{{ $categoria->productos_count }} productos</p>

            <div class="flex items-center gap-2 mt-4">
                <a href="{{ route('categorias.edit', $categoria) }}" class="flex-1 text-center bg-gray-700 hover:bg-gray-600 text-white text-xs py-1.5 rounded-lg transition-colors">
                    <i class="fas fa-pen-to-square mr-1"></i> Editar
                </a>
                <form action="{{ route('categorias.toggle', $categoria) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-xs px-2 py-1.5 rounded-lg transition-colors {{ $categoria->activo ? 'text-yellow-400' : 'text-green-400' }}">
                        <i class="fas fa-{{ $categoria->activo ? 'eye-slash' : 'eye' }}"></i>
                    </button>
                </form>
                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-gray-700 hover:bg-red-500/20 text-red-400 text-xs px-2 py-1.5 rounded-lg transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-gray-500">
        <i class="fas fa-tags text-4xl mb-3 block"></i>
        <p>No hay categorías registradas</p>
        <a href="{{ route('categorias.create') }}" class="mt-3 inline-block text-primary hover:underline text-sm">Crear primera categoría</a>
    </div>
    @endforelse
</div>
@endsection
