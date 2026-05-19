@extends('layouts.app')
@section('title', 'Editar Categoría')
@section('page-title', 'Editar Categoría')
@section('page-subtitle', $categoria->nombre)

@section('content')
<div class="max-w-lg">
    <div class="card rounded-xl p-6">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required maxlength="100"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Descripción</label>
                <textarea name="descripcion" rows="2"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition resize-none">{{ old('descripcion', $categoria->descripcion) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Ícono</label>
                    <input type="text" name="icono" value="{{ old('icono', $categoria->icono) }}"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Color</label>
                    <input type="color" name="color" value="{{ old('color', $categoria->color) }}"
                        class="h-10 w-full bg-gray-800 border border-gray-700 rounded-lg cursor-pointer">
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Orden</label>
                <input type="number" name="orden" value="{{ old('orden', $categoria->orden) }}" min="0"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ $categoria->activo ? 'checked' : '' }} class="accent-primary">
                <label for="activo" class="text-sm text-gray-400">Categoría activa</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Actualizar
                </button>
                <a href="{{ route('categorias.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
