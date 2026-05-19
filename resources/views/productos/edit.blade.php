@extends('layouts.app')
@section('title', 'Editar Producto')
@section('page-title', 'Editar Producto')
@section('page-subtitle', $producto->nombre)

@section('content')
<div class="max-w-2xl">
    <div class="card rounded-xl p-6">
        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm text-gray-400 mb-1">Nombre <span class="text-red-400">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Categoría <span class="text-red-400">*</span></label>
                    <select name="categoria_id" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ $producto->categoria_id == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Precio <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                        <input type="number" name="precio" value="{{ old('precio', $producto->precio) }}" required step="0.01" min="0"
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-7 pr-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Impuesto (%)</label>
                    <input type="number" name="impuesto" value="{{ old('impuesto', $producto->impuesto) }}" step="0.01"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $producto->stock) }}" min="0"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Stock mínimo</label>
                    <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" min="0"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Código de barra</label>
                    <input type="text" name="codigo_barra" value="{{ old('codigo_barra', $producto->codigo_barra) }}"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tiempo preparación (min)</label>
                    <input type="number" name="tiempo_preparacion" value="{{ old('tiempo_preparacion', $producto->tiempo_preparacion) }}" min="0"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm text-gray-400 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="2"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition resize-none">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm text-gray-400 mb-1">Nueva imagen (dejar vacío para mantener actual)</label>
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-16 h-16 rounded-lg object-cover mb-2">
                    @endif
                    <input type="file" name="imagen" accept="image/*"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:outline-none cursor-pointer">
                </div>
            </div>

            {{-- Variantes --}}
            <div class="border border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-white">Variantes</h4>
                    <button type="button" id="addVariante" class="text-xs bg-primary/20 text-primary px-3 py-1 rounded-lg hover:bg-primary/30 transition">
                        <i class="fas fa-plus mr-1"></i> Agregar
                    </button>
                </div>
                <div id="variantes-container" class="space-y-2">
                    @foreach($producto->variantes as $i => $v)
                    <div class="flex gap-2 items-center">
                        <input type="text" name="variantes[{{ $i }}][nombre]" value="{{ $v->nombre }}" required
                            class="flex-1 bg-gray-700 border border-gray-600 text-white rounded px-3 py-1.5 text-sm focus:outline-none focus:border-primary">
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-gray-400 text-xs">$</span>
                            <input type="number" name="variantes[{{ $i }}][precio]" value="{{ $v->precio }}" step="0.01" min="0" required
                                class="w-24 bg-gray-700 border border-gray-600 text-white rounded pl-5 pr-2 py-1.5 text-sm focus:outline-none focus:border-primary">
                        </div>
                        <input type="number" name="variantes[{{ $i }}][stock]" value="{{ $v->stock }}" min="0"
                            class="w-16 bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-sm">
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ $producto->activo ? 'checked' : '' }} class="accent-primary">
                <label for="activo" class="text-sm text-gray-400">Producto activo</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Actualizar
                </button>
                <a href="{{ route('productos.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
let varianteCount = {{ $producto->variantes->count() }};
document.getElementById('addVariante').addEventListener('click', () => {
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center';
    div.innerHTML = `
        <input type="text" name="variantes[${varianteCount}][nombre]" placeholder="Ej: Grande" required
            class="flex-1 bg-gray-700 border border-gray-600 text-white rounded px-3 py-1.5 text-sm focus:outline-none focus:border-primary">
        <div class="relative">
            <span class="absolute left-2 top-2 text-gray-400 text-xs">$</span>
            <input type="number" name="variantes[${varianteCount}][precio]" placeholder="0.00" step="0.01" min="0" required
                class="w-24 bg-gray-700 border border-gray-600 text-white rounded pl-5 pr-2 py-1.5 text-sm focus:outline-none focus:border-primary">
        </div>
        <input type="number" name="variantes[${varianteCount}][stock]" placeholder="Stock" min="0" value="0"
            class="w-16 bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-sm">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 text-xs"><i class="fas fa-times"></i></button>
    `;
    document.getElementById('variantes-container').appendChild(div);
    varianteCount++;
});
</script>
@endpush
