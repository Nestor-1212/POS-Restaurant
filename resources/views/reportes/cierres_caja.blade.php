@extends('layouts.app')
@section('title', 'Cierres de Caja')
@section('page-title', 'Cierres de Caja')

@section('content')
{{-- Abrir caja --}}
<div class="card rounded-xl p-4 mb-6">
    <h3 class="font-semibold text-white mb-3">Abrir Nueva Caja</h3>
    <form action="{{ route('reportes.abrir-caja') }}" method="POST" class="flex gap-3 items-end">
        @csrf
        <div>
            <label class="block text-xs text-gray-400 mb-1">Saldo inicial ($)</label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">$</span>
                <input type="number" name="saldo_inicial" step="0.01" min="0" required
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg pl-7 pr-4 py-2 text-sm focus:outline-none focus:border-primary w-36"
                    placeholder="0.00">
            </div>
        </div>
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
            <i class="fas fa-lock-open mr-1"></i> Abrir Caja
        </button>
    </form>
</div>

{{-- History --}}
<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Cajero</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Apertura</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Cierre</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Ventas</th>
                <th class="text-right text-gray-400 font-medium px-4 py-3">Total</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($cierres as $cierre)
            <tr class="hover:bg-gray-800/30 transition-colors">
                <td class="px-4 py-3 text-white">{{ $cierre->usuario->name }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $cierre->apertura_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">
                    {{ $cierre->cierre_at ? $cierre->cierre_at->format('d/m/Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-right text-gray-300">{{ $cierre->num_ventas }}</td>
                <td class="px-4 py-3 text-right font-bold text-white">${{ number_format($cierre->total_ventas, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    @if(!$cierre->cierre_at)
                    <form action="{{ route('reportes.cerrar-caja', $cierre) }}" method="POST" class="inline"
                        onsubmit="return confirm('¿Cerrar la caja ahora?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs bg-red-500/20 text-red-400 hover:bg-red-500/30 px-3 py-1 rounded-lg transition-colors">
                            <i class="fas fa-lock mr-1"></i> Cerrar
                        </button>
                    </form>
                    @else
                    <span class="text-xs text-green-400"><i class="fas fa-check mr-1"></i>Cerrado</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-gray-500">No hay cierres registrados</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">{{ $cierres->links() }}</div>
</div>
@endsection
