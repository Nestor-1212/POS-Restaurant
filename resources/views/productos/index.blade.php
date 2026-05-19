@extends('layouts.app')
@section('title', 'Productos')
@section('page-title', 'Productos')
@section('page-subtitle', 'Gestión de productos del menú')

@section('content')
{{-- Filters --}}
<div class="flex flex-wrap gap-3 mb-6">
    <form action="{{ route('productos.index') }}" method="GET" class="flex flex-wrap gap-3 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar producto..."
            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary w-56">
        <select name="categoria_id" class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
            <i class="fas fa-search"></i>
        </button>
    </form>
    <a href="{{ route('productos.create') }}" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

{{-- Table --}}
<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3 w-12">#</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Producto</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Categoría</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Precio</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Stock</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Estado</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($productos as $producto)
            <tr class="hover:bg-gray-800/30 transition-colors">
                <td class="px-4 py-3 text-gray-500">{{ $producto->id }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-burger text-primary text-sm"></i>
                            </div>
                        @endif
                        <div>
                            <p class="text-white font-medium">{{ $producto->nombre }}</p>
                            @if($producto->tiene_variantes)
                                <p class="text-xs text-primary">Con variantes</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-400">{{ $producto->categoria->nombre }}</td>
                <td class="px-4 py-3 text-right text-white font-medium">${{ number_format($producto->precio, 2) }}</td>
                <td class="px-4 py-3 text-right">
                    <span class="{{ $producto->stock_bajo ? 'text-red-400' : 'text-gray-300' }}">
                        {{ $producto->stock }}
                        @if($producto->stock_bajo)
                            <i class="fas fa-triangle-exclamation text-xs ml-1"></i>
                        @endif
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full {{ $producto->activo ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-500' }}">
                        {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('productos.edit', $producto) }}" class="p-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg text-gray-300 transition-colors" title="Editar">
                            <i class="fas fa-pen-to-square text-xs"></i>
                        </a>
                        <form action="{{ route('productos.toggle', $producto) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="p-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors {{ $producto->activo ? 'text-yellow-400' : 'text-green-400' }}" title="{{ $producto->activo ? 'Desactivar' : 'Activar' }}">
                                <i class="fas fa-{{ $producto->activo ? 'eye-slash' : 'eye' }} text-xs"></i>
                            </button>
                        </form>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 bg-gray-700 hover:bg-red-500/20 text-red-400 rounded-lg transition-colors" title="Eliminar">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-500">
                    <i class="fas fa-burger text-3xl mb-2 block"></i>
                    No hay productos registrados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">
        {{ $productos->links() }}
    </div>
</div>
@endsection
