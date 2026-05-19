@extends('layouts.app')
@section('title', 'Nueva Mesa')
@section('page-title', 'Nueva Mesa')

@section('content')
<div class="max-w-md">
    <div class="card rounded-xl p-6">
        <form action="{{ route('mesas.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="50"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition"
                    placeholder="Ej: Mesa 1, Mesa VIP, Barra...">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Capacidad <span class="text-red-400">*</span></label>
                <input type="number" name="capacidad" value="{{ old('capacidad', 4) }}" required min="1"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Ubicación</label>
                <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" maxlength="100"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition"
                    placeholder="Ej: Interior, Terraza, Jardín...">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Guardar
                </button>
                <a href="{{ route('mesas.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
