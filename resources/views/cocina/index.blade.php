<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cocina — POS Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #111; }
        .pedido-card { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity:0; transform:translateY(10px) } to { opacity:1; transform:translateY(0) } }
        .blink { animation: blink 1s step-end infinite; }
        @keyframes blink { 50% { opacity: 0 } }
    </style>
</head>
<body class="text-white min-h-screen">

{{-- Header --}}
<header class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between sticky top-0 z-10">
    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-fire-burner text-white"></i>
            </div>
            <div>
                <p class="font-bold">COCINA</p>
                <p class="text-xs text-gray-400" id="reloj-cocina">{{ now()->format('H:i:s') }}</p>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-center">
            <p class="text-2xl font-bold text-yellow-400" id="contador-pedidos">{{ $pedidos->count() }}</p>
            <p class="text-xs text-gray-400">Pedidos</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="text-gray-400 hover:text-red-400 text-sm transition-colors">
                <i class="fas fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</header>

{{-- Content --}}
<div class="p-4">
    {{-- Empty state --}}
    <div id="empty-cocina" class="{{ $pedidos->count() > 0 ? 'hidden' : '' }} flex flex-col items-center justify-center h-64 text-gray-600">
        <i class="fas fa-check-circle text-6xl text-green-500/40 mb-4"></i>
        <p class="text-xl font-medium">¡Todo listo!</p>
        <p class="text-sm">No hay pedidos pendientes</p>
    </div>

    {{-- Orders Grid --}}
    <div id="orders-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($pedidos as $pedido)
        <div class="pedido-card bg-gray-900 border-2 rounded-xl overflow-hidden {{ $pedido->estado === 'en_preparacion' ? 'border-yellow-500' : 'border-gray-700' }}"
            id="pedido-{{ $pedido->id }}">

            {{-- Card Header --}}
            <div class="px-4 py-3 {{ $pedido->estado === 'en_preparacion' ? 'bg-yellow-500/20' : 'bg-gray-800' }} flex items-center justify-between">
                <div>
                    <p class="font-bold text-white">{{ $pedido->mesa ? $pedido->mesa->nombre : 'Sin Mesa' }}</p>
                    <p class="text-xs text-gray-400">{{ $pedido->numero_factura }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs px-2 py-1 rounded-full {{ $pedido->estado === 'pendiente' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                        {{ $pedido->estado === 'pendiente' ? 'NUEVO' : 'EN PREP.' }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">{{ $pedido->created_at->format('H:i') }}</p>
                </div>
            </div>

            {{-- Items --}}
            <div class="p-4 space-y-2">
                @foreach($pedido->detalles as $d)
                <div class="flex items-start gap-3 py-1 border-b border-gray-800 last:border-0 cursor-pointer hover:bg-gray-800/50 rounded px-1 transition-colors"
                    onclick="marcarDetallePreparado({{ $d->id }}, this)"
                    id="detalle-{{ $d->id }}">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                        {{ $d->estado === 'listo' ? 'bg-green-500/20 border-2 border-green-500' : 'bg-gray-700 border-2 border-gray-600' }}">
                        @if($d->estado === 'listo')
                            <i class="fas fa-check text-green-400 text-xs"></i>
                        @else
                            <span class="text-white text-xs font-bold">{{ $d->cantidad }}</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-white font-medium {{ $d->estado === 'listo' ? 'line-through text-gray-500' : '' }}">
                            {{ $d->producto->nombre }}
                            @if($d->variante) <span class="text-gray-400">({{ $d->variante->nombre }})</span> @endif
                        </p>
                        @if($d->notas)
                            <p class="text-xs text-yellow-400 mt-0.5"><i class="fas fa-note-sticky mr-1"></i>{{ $d->notas }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="px-4 pb-4 flex gap-2">
                @if($pedido->estado === 'pendiente')
                <button onclick="marcarEnPreparacion({{ $pedido->id }}, this)"
                    class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-fire mr-1"></i> Preparar
                </button>
                @elseif($pedido->estado === 'en_preparacion')
                <button onclick="marcarListo({{ $pedido->id }}, this)"
                    class="flex-1 bg-green-500 hover:bg-green-400 text-white font-bold py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-check mr-1"></i> Listo
                </button>
                @endif
            </div>

            {{-- Elapsed Time --}}
            <div class="px-4 pb-3">
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <i class="fas fa-clock"></i>
                    <span class="timer" data-start="{{ $pedido->created_at->timestamp }}">Calculando...</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Timers
function updateTimers() {
    const now = Math.floor(Date.now() / 1000);
    document.querySelectorAll('.timer').forEach(el => {
        const start = parseInt(el.dataset.start);
        const diff = now - start;
        const mins = Math.floor(diff / 60);
        const secs = diff % 60;
        el.textContent = `${mins}m ${secs}s`;
        if (diff > 600) el.style.color = '#ef4444'; // red after 10min
        else if (diff > 300) el.style.color = '#f59e0b'; // yellow after 5min
        else el.style.color = '';
    });
}
setInterval(updateTimers, 1000);
updateTimers();

// Clock
setInterval(() => {
    document.getElementById('reloj-cocina').textContent = new Date().toLocaleTimeString('es');
}, 1000);

async function marcarEnPreparacion(id, btn) {
    btn.disabled = true;
    const res = await fetch(`{{ url('/cocina/venta') }}/${id}/preparacion`, {
        method: 'PATCH',
        headers: {'X-CSRF-TOKEN': CSRF}
    });
    const data = await res.json();
    if (data.success) {
        const card = document.getElementById(`pedido-${id}`);
        card.classList.remove('border-gray-700');
        card.classList.add('border-yellow-500');
        card.querySelector('.text-xs.px-2').textContent = 'EN PREP.';
        card.querySelector('.text-xs.px-2').className = 'text-xs px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-400';
        btn.className = 'flex-1 bg-green-500 hover:bg-green-400 text-white font-bold py-2 rounded-lg text-sm transition-colors';
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Listo';
        btn.onclick = function() { marcarListo(id, this); };
        btn.disabled = false;
    }
}

async function marcarListo(id, btn) {
    btn.disabled = true;
    const res = await fetch(`{{ url('/cocina/venta') }}/${id}/listo`, {
        method: 'PATCH',
        headers: {'X-CSRF-TOKEN': CSRF}
    });
    const data = await res.json();
    if (data.success) {
        const card = document.getElementById(`pedido-${id}`);
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';
        setTimeout(() => {
            card.remove();
            const count = document.querySelectorAll('[id^="pedido-"]').length;
            document.getElementById('contador-pedidos').textContent = count;
            if (count === 0) document.getElementById('empty-cocina').classList.remove('hidden');
        }, 500);
    }
}

async function marcarDetallePreparado(id, row) {
    const res = await fetch(`{{ url('/cocina/detalle') }}/${id}/preparado`, {
        method: 'PATCH',
        headers: {'X-CSRF-TOKEN': CSRF}
    });
    const data = await res.json();
    if (data.success) {
        const circle = row.querySelector('div');
        circle.className = 'w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 bg-green-500/20 border-2 border-green-500';
        circle.innerHTML = '<i class="fas fa-check text-green-400 text-xs"></i>';
        const name = row.querySelector('p');
        name.classList.add('line-through', 'text-gray-500');
    }
}

// Auto-refresh every 30 seconds
setInterval(() => { window.location.reload(); }, 30000);
</script>
</body>
</html>
