@extends('layouts.app')
@section('title', 'Editar Mesa')
@section('page-title', 'Editar Mesa')
@section('page-subtitle', $mesa->nombre)

@section('content')
<div class="max-w-md">
    <div class="card rounded-xl p-6">
        <form action="{{ route('mesas.update', $mesa) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $mesa->nombre) }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Capacidad</label>
                <input type="number" name="capacidad" value="{{ old('capacidad', $mesa->capacidad) }}" min="1"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Ubicación</label>
                <input type="text" name="ubicacion" value="{{ old('ubicacion', $mesa->ubicacion) }}"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Estado</label>
                <select name="estado" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                    @foreach(['disponible', 'ocupada', 'reservada', 'inactiva'] as $estado)
                    <option value="{{ $estado }}" {{ $mesa->estado === $estado ? 'selected' : '' }} class="capitalize">{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Actualizar
                </button>
                <a href="{{ route('mesas.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
