@extends('layouts.app')
@section('title', 'Nueva Categoría')
@section('page-title', 'Nueva Categoría')
@section('page-subtitle', 'Agregar categoría de productos')

@section('content')
<div class="max-w-lg">
    <div class="card rounded-xl p-6">
        <form action="{{ route('categorias.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="100"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition"
                    placeholder="Ej: Bebidas, Carnes, Postres...">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Descripción</label>
                <textarea name="descripcion" rows="2" maxlength="255"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition resize-none"
                    placeholder="Descripción opcional...">{{ old('descripcion') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Ícono (FontAwesome)</label>
                    <input type="text" name="icono" value="{{ old('icono', 'tag') }}" maxlength="50"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition"
                        placeholder="burger, glass-water, pizza...">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Color</label>
                    <div class="flex gap-2">
                        <input type="color" name="color" value="{{ old('color', '#FF6B35') }}"
                            class="h-10 w-14 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer">
                        <input type="text" id="colorText" value="{{ old('color', '#FF6B35') }}"
                            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none"
                            placeholder="#FF6B35">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Orden</label>
                <input type="number" name="orden" value="{{ old('orden', 0) }}" min="0"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Guardar
                </button>
                <a href="{{ route('categorias.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
