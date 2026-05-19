@extends('layouts.app')
@section('title', 'Inventario')
@section('page-title', 'Inventario')
@section('page-subtitle', 'Control de stock de productos')

@section('content')
<div class="card rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-800/50 border-b border-gray-700">
            <tr>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Producto</th>
                <th class="text-left text-gray-400 font-medium px-4 py-3">Categoría</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Stock</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Mínimo</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Estado</th>
                <th class="text-center text-gray-400 font-medium px-4 py-3">Ajuste</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @forelse($productos as $producto)
            <tr class="hover:bg-gray-800/30 transition-colors {{ $producto->stock_bajo ? 'bg-red-500/5' : '' }}">
                <td class="px-4 py-3">
                    <p class="text-white font-medium">{{ $producto->nombre }}</p>
                </td>
                <td class="px-4 py-3 text-gray-400">{{ $producto->categoria->nombre }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="font-bold {{ $producto->stock_bajo ? 'text-red-400' : 'text-white' }}">{{ $producto->stock }}</span>
                </td>
                <td class="px-4 py-3 text-center text-gray-400">{{ $producto->stock_minimo }}</td>
                <td class="px-4 py-3 text-center">
                    @if($producto->stock === 0)
                        <span class="text-xs px-2 py-1 rounded-full bg-red-500/20 text-red-400">Agotado</span>
                    @elseif($producto->stock_bajo)
                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-400">Bajo</span>
                    @else
                        <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-400">OK</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-xs bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-arrows-up-down mr-1"></i> Ajustar
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 top-8 z-10 bg-gray-800 border border-gray-700 rounded-xl p-3 shadow-2xl w-56">
                            <form action="{{ route('productos.ajuste-stock', $producto) }}" method="POST" class="space-y-2">
                                @csrf
                                <select name="tipo" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-2 py-1.5 text-xs">
                                    <option value="entrada">Entrada (+)</option>
                                    <option value="salida">Salida (-)</option>
                                    <option value="ajuste">Ajuste (= cantidad)</option>
                                </select>
                                <input type="number" name="cantidad" min="1" required placeholder="Cantidad"
                                    class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-2 py-1.5 text-xs">
                                <input type="text" name="motivo" placeholder="Motivo (opcional)"
                                    class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-2 py-1.5 text-xs">
                                <button type="submit" class="w-full bg-primary hover:bg-orange-600 text-white py-1.5 rounded-lg text-xs transition-colors">
                                    Aplicar
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-gray-500">No hay productos</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-800">{{ $productos->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
// Simple Alpine-like toggle without Alpine dependency
document.querySelectorAll('[\\@click]').forEach(el => {
    const target = el.getAttribute('@click');
    if (target === 'open = !open') {
        const dropdown = el.nextElementSibling;
        el.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'none' ? '' : 'none';
        });
        document.addEventListener('click', () => { dropdown.style.display = 'none'; });
    }
});
</script>
@endpush
