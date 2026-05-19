@extends('layouts.app')
@section('title', 'Nuevo Usuario')
@section('page-title', 'Nuevo Usuario')

@section('content')
<div class="max-w-lg">
    <div class="card rounded-xl p-6">
        <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre completo <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Email <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" maxlength="20"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Rol <span class="text-red-400">*</span></label>
                <select name="role_id" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Contraseña <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required minlength="6"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Confirmar <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Crear Usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
