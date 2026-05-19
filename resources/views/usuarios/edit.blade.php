@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')
@section('page-subtitle', $usuario->name)

@section('content')
<div class="max-w-lg">
    <div class="card rounded-xl p-6">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Email <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Rol <span class="text-red-400">*</span></label>
                <select name="role_id" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $usuario->role_id == $role->id ? 'selected' : '' }}>{{ $role->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nueva contraseña</label>
                    <input type="password" name="password" minlength="6"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition"
                        placeholder="Dejar vacío para no cambiar">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Confirmar</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ $usuario->activo ? 'checked' : '' }} class="accent-primary">
                <label for="activo" class="text-sm text-gray-400">Usuario activo</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Actualizar
                </button>
                <a href="{{ route('usuarios.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
