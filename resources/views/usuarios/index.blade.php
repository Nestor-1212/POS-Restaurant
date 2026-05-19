@extends('layouts.app')
@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Gestión de usuarios del sistema')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-400 text-sm">{{ $usuarios->total() }} usuarios registrados</p>
    <a href="{{ route('usuarios.create') }}" class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i> Nuevo Usuario
    </a>
</div>

<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Usuario</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Email</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Rol</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Estado</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($usuarios as $usuario)
            <tr class="hover:bg-gray-800/30 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-primary/20 rounded-full flex items-center justify-center">
                            <span class="text-primary font-bold text-sm">{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-white font-medium">{{ $usuario->name }}</p>
                            @if($usuario->telefono)
                                <p class="text-xs text-gray-500">{{ $usuario->telefono }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-400">{{ $usuario->email }}</td>
                <td class="px-4 py-3">
                    @php
                        $roleColors = ['Admin' => 'bg-red-500/20 text-red-400', 'Cajero' => 'bg-blue-500/20 text-blue-400', 'Cocina' => 'bg-orange-500/20 text-orange-400', 'Supervisor' => 'bg-purple-500/20 text-purple-400'];
                        $color = $roleColors[$usuario->role->nombre] ?? 'bg-gray-700 text-gray-400';
                    @endphp
                    <span class="text-xs px-2 py-1 rounded-full {{ $color }}">{{ $usuario->role->nombre }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full {{ $usuario->activo ? 'bg-green-500/20 text-green-400' : 'bg-gray-700 text-gray-500' }}">
                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="p-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg text-gray-300 transition-colors">
                            <i class="fas fa-pen-to-square text-xs"></i>
                        </a>
                        @if($usuario->id !== auth()->id())
                        <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="p-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors {{ $usuario->activo ? 'text-yellow-400' : 'text-green-400' }}">
                                <i class="fas fa-{{ $usuario->activo ? 'eye-slash' : 'eye' }} text-xs"></i>
                            </button>
                        </form>
                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 bg-gray-700 hover:bg-red-500/20 text-red-400 rounded-lg transition-colors">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-12 text-gray-500">No hay usuarios registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">{{ $usuarios->links() }}</div>
</div>
@endsection
