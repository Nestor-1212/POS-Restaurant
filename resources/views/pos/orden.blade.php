<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS — {{ $mesa ? $mesa->nombre : 'Venta Directa' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #0d1117; overflow: hidden; height: 100vh; }
        .cat-btn.active { background: #FF6B35; color: white; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
        .product-btn { transition: transform 0.1s, box-shadow 0.1s; }
        .product-btn:active { transform: scale(0.97); }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-thumb { background: #FF6B35; border-radius: 2px; }
        .order-item:hover { background: rgba(255,107,53,0.08); }
    </style>
</head>
<body class="text-white flex flex-col h-screen">

{{-- Top Bar --}}
<header class="bg-gray-900 border-b border-gray-800 px-4 py-2 flex items-center justify-between flex-shrink-0">
    <div class="flex items-center gap-3">
        <a href="{{ route('pos.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
            <i class="fas fa-utensils text-white text-sm"></i>
        </div>
        <div>
            <p class="font-bold text-sm">{{ $mesa ? $mesa->nombre : 'Venta Directa' }}</p>
            <p class="text-xs text-gray-400">{{ auth()->user()->name }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <select id="mesa-select" class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1 text-xs">
            <option value="">Sin mesa</option>
            @foreach($mesas as $m)
                <option value="{{ $m->id }}" {{ $mesa && $mesa->id == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
            @endforeach
        </select>
        <span class="text-xs text-gray-400" id="reloj">{{ now()->format('H:i') }}</span>
    </div>
</header>

{{-- Main POS Layout --}}
<div class="flex flex-1 overflow-hidden">

    {{-- LEFT: Products --}}
    <div class="flex-1 flex flex-col bg-gray-950 overflow-hidden">

        {{-- Category Bar --}}
        <div class="flex gap-2 p-3 overflow-x-auto flex-shrink-0 bg-gray-900 border-b border-gray-800">
            <button class="cat-btn active flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium bg-primary text-white transition-all" data-cat="all">
                <i class="fas fa-th mr-1"></i> Todos
            </button>
            @foreach($categorias as $cat)
            <button class="cat-btn flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium bg-gray-800 text-gray-300 hover:bg-gray-700 transition-all" data-cat="{{ $cat->id }}"
                style="border-left: 3px solid {{ $cat->color ?? '#FF6B35' }}">
                <i class="fas fa-{{ $cat->icono ?? 'tag' }} mr-1"></i> {{ $cat->nombre }}
            </button>
            @endforeach
        </div>

        {{-- Search --}}
        <div class="px-3 py-2 bg-gray-900 flex-shrink-0">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-500 text-sm"></i>
                <input type="text" id="search-product" placeholder="Buscar producto..."
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:border-primary transition">
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 overflow-y-auto p-3">
            <div class="product-grid" id="products-grid">
                @foreach($categorias as $cat)
                @foreach($cat->productosActivos as $producto)
                <button class="product-btn product-card bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-primary/50 rounded-xl p-3 text-left transition-all"
                    data-cat="{{ $cat->id }}"
                    data-id="{{ $producto->id }}"
                    data-nombre="{{ $producto->nombre }}"
                    data-precio="{{ $producto->precio }}"
                    data-variantes="{{ $producto->tiene_variantes ? $producto->variantes->toJson() : '[]' }}"
                    onclick="seleccionarProducto(this)">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-full h-20 object-cover rounded-lg mb-2">
                    @else
                        <div class="w-full h-20 rounded-lg mb-2 flex items-center justify-center" style="background: {{ $cat->color ?? '#FF6B35' }}22">
                            <i class="fas fa-{{ $cat->icono ?? 'burger' }} text-3xl" style="color: {{ $cat->color ?? '#FF6B35' }}"></i>
                        </div>
                    @endif
                    <p class="text-sm font-medium text-white leading-tight line-clamp-2">{{ $producto->nombre }}</p>
                    <p class="text-primary font-bold text-sm mt-1">${{ number_format($producto->precio, 2) }}</p>
                    @if($producto->stock <= $producto->stock_minimo)
                        <p class="text-xs text-red-400 mt-0.5"><i class="fas fa-triangle-exclamation mr-1"></i>Stock bajo</p>
                    @endif
                </button>
                @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- RIGHT: Order --}}
    <div class="w-80 xl:w-96 flex flex-col bg-gray-900 border-l border-gray-800 flex-shrink-0">

        {{-- Order Header --}}
        <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between flex-shrink-0">
            <div>
                <p class="font-bold text-white">Pedido Actual</p>
                <p class="text-xs text-gray-400" id="factura-num">Nueva orden</p>
            </div>
            <button onclick="limpiarOrden()" class="text-xs text-red-400 hover:text-red-300 bg-red-400/10 px-2 py-1 rounded-lg transition">
                <i class="fas fa-trash mr-1"></i> Limpiar
            </button>
        </div>

        {{-- Order Items --}}
        <div class="flex-1 overflow-y-auto px-3 py-2" id="order-items">
            <div id="empty-order" class="flex flex-col items-center justify-center h-full text-gray-600">
                <i class="fas fa-shopping-bag text-4xl mb-3"></i>
                <p class="text-sm">Agrega productos</p>
            </div>
        </div>

        {{-- Totals --}}
        <div class="border-t border-gray-800 px-4 py-3 space-y-1.5 flex-shrink-0 bg-gray-900">
            <div class="flex justify-between text-sm text-gray-400">
                <span>Subtotal</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="flex justify-between text-sm text-gray-400">
                <span>ITBMS (7%)</span>
                <span id="impuesto">$0.00</span>
            </div>
            <div class="flex justify-between text-sm items-center">
                <span class="text-gray-400">Descuento</span>
                <div class="flex items-center gap-1">
                    <span class="text-gray-500">$</span>
                    <input type="number" id="descuento-input" value="0" min="0" step="0.01"
                        class="w-16 bg-gray-800 border border-gray-700 text-white rounded px-2 py-0.5 text-sm text-right focus:outline-none"
                        onchange="calcularTotales()">
                </div>
            </div>
            <div class="flex justify-between text-lg font-bold text-white border-t border-gray-700 pt-2 mt-1">
                <span>TOTAL</span>
                <span id="total" class="text-primary">$0.00</span>
            </div>
        </div>

        {{-- Pay Button --}}
        <div class="px-4 pb-4 pt-2 flex-shrink-0">
            <button onclick="abrirModalPago()" id="btn-cobrar"
                class="w-full bg-primary hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white py-3 rounded-xl font-bold text-lg transition-colors flex items-center justify-center gap-2"
                disabled>
                <i class="fas fa-cash-register"></i> COBRAR
            </button>
        </div>
    </div>
</div>

{{-- Modal Variantes --}}
<div id="modal-variantes" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 w-full max-w-sm">
        <h3 class="font-bold text-white mb-1" id="variante-nombre">Producto</h3>
        <p class="text-gray-400 text-sm mb-4">Selecciona el tamaño:</p>
        <div id="variante-opciones" class="grid grid-cols-2 gap-3 mb-4"></div>
        <button onclick="cerrarModalVariantes()" class="w-full bg-gray-700 text-gray-300 py-2 rounded-xl text-sm">Cancelar</button>
    </div>
</div>

{{-- Modal Pago --}}
<div id="modal-pago" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-white text-lg">Cobrar Pedido</h3>
            <button onclick="cerrarModalPago()" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <div class="bg-primary/10 border border-primary/30 rounded-xl p-4 mb-4 text-center">
            <p class="text-gray-400 text-sm">Total a Cobrar</p>
            <p class="text-3xl font-bold text-primary" id="modal-total">$0.00</p>
        </div>

        <div class="space-y-3 mb-4">
            <div>
                <label class="block text-xs text-gray-400 mb-1">Método de Pago</label>
                <div class="grid grid-cols-3 gap-2" id="metodos-pago">
                    @foreach(['efectivo' => ['fas fa-money-bill', 'Efectivo'], 'tarjeta' => ['fas fa-credit-card', 'Tarjeta'], 'yappy' => ['fas fa-mobile-screen', 'Yappy'], 'ach' => ['fas fa-building-columns', 'ACH'], 'transferencia' => ['fas fa-arrow-right-arrow-left', 'Transf.'], 'mixto' => ['fas fa-layer-group', 'Mixto']] as $val => [$icon, $label])
                    <button class="metodo-btn border border-gray-700 hover:border-primary rounded-xl py-2 text-xs text-gray-300 hover:text-white transition-all flex flex-col items-center gap-1"
                        data-metodo="{{ $val }}" onclick="seleccionarMetodo('{{ $val }}', this)">
                        <i class="{{ $icon }} text-base"></i> {{ $label }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" id="metodo-seleccionado" value="efectivo">
            </div>
            <div id="seccion-efectivo">
                <label class="block text-xs text-gray-400 mb-1">Monto Recibido</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">$</span>
                    <input type="number" id="monto-recibido" step="0.01" min="0" placeholder="0.00"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:border-primary"
                        oninput="calcularCambio()">
                </div>
                <div class="mt-2 flex justify-between text-sm">
                    <span class="text-gray-400">Cambio:</span>
                    <span id="cambio-display" class="text-green-400 font-bold">$0.00</span>
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Cliente (opcional)</label>
                <input type="text" id="cliente-nombre" placeholder="Nombre del cliente"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">RUC / NIT</label>
                <input type="text" id="cliente-ruc" placeholder="00-000-000"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button onclick="cerrarModalPago()" class="bg-gray-700 hover:bg-gray-600 text-gray-300 py-3 rounded-xl font-medium transition-colors">
                Cancelar
            </button>
            <button onclick="procesarPago()" class="bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-bold transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Confirmar
            </button>
        </div>
    </div>
</div>

<script>
// State
let items = {};
let ventaId = null;
let mesaId = {{ $mesa ? $mesa->id : 'null' }};

// Init
const initVenta = {{ $ventaActiva ? 'true' : 'false' }};
@if($ventaActiva)
ventaId = {{ $ventaActiva->id }};
document.getElementById('factura-num').textContent = '{{ $ventaActiva->numero_factura }}';
@foreach($ventaActiva->detalles as $d)
items['{{ $d->id }}'] = {
    id: {{ $d->id }},
    producto_id: {{ $d->producto_id }},
    variante_id: {{ $d->variante_id ?? 'null' }},
    nombre: '{{ $d->producto->nombre }}{{ $d->variante ? " (" . $d->variante->nombre . ")" : "" }}',
    precio: {{ $d->precio_unitario }},
    cantidad: {{ $d->cantidad }},
    subtotal: {{ $d->subtotal }},
};
@endforeach
renderOrder();
@endif

// Clock
setInterval(() => {
    document.getElementById('reloj').textContent = new Date().toLocaleTimeString('es', {hour:'2-digit',minute:'2-digit'});
}, 1000);

// Category filter
document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.cat-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = '';
            b.style.color = '';
        });
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
        });
    });
});

// Search
document.getElementById('search-product').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        card.style.display = card.dataset.nombre.toLowerCase().includes(q) ? '' : 'none';
    });
});

function seleccionarProducto(btn) {
    const variantes = JSON.parse(btn.dataset.variantes || '[]');
    if (variantes.length > 0) {
        mostrarModalVariantes(btn, variantes);
    } else {
        agregarProducto(btn.dataset.id, null, btn.dataset.nombre, parseFloat(btn.dataset.precio));
    }
}

function mostrarModalVariantes(btn, variantes) {
    document.getElementById('variante-nombre').textContent = btn.dataset.nombre;
    const cont = document.getElementById('variante-opciones');
    cont.innerHTML = '';
    variantes.forEach(v => {
        const b = document.createElement('button');
        b.className = 'border border-gray-700 hover:border-primary rounded-xl p-3 text-left transition-all hover:bg-primary/10';
        b.innerHTML = `<p class="font-medium text-white text-sm">${v.nombre}</p><p class="text-primary text-sm">$${parseFloat(v.precio).toFixed(2)}</p>`;
        b.onclick = () => {
            cerrarModalVariantes();
            agregarProducto(btn.dataset.id, v.id, btn.dataset.nombre + ' (' + v.nombre + ')', parseFloat(v.precio));
        };
        cont.appendChild(b);
    });
    document.getElementById('modal-variantes').classList.remove('hidden');
}

function cerrarModalVariantes() {
    document.getElementById('modal-variantes').classList.add('hidden');
}

async function agregarProducto(productoId, varianteId, nombre, precio) {
    const res = await fetch('{{ route("pos.agregar") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({mesa_id: mesaId || document.getElementById('mesa-select').value || null, producto_id: productoId, variante_id: varianteId, cantidad: 1})
    });
    const data = await res.json();
    if (data.success) await recargarOrden();
}

async function recargarOrden() {
    const mesaIdVal = mesaId || document.getElementById('mesa-select').value || null;
    if (!mesaIdVal && !ventaId) { renderOrder(); return; }
    const url = mesaIdVal ? `{{ url('/pos/venta') }}/${mesaIdVal}` : `{{ url('/pos/venta') }}`;
    const res = await fetch(url);
    const data = await res.json();
    if (data.venta) {
        ventaId = data.venta.id;
        document.getElementById('factura-num').textContent = data.venta.numero_factura;
        items = {};
        data.venta.detalles.forEach(d => {
            items[d.id] = {
                id: d.id, producto_id: d.producto_id, variante_id: d.variante_id,
                nombre: d.producto.nombre + (d.variante ? ` (${d.variante.nombre})` : ''),
                precio: parseFloat(d.precio_unitario), cantidad: d.cantidad, subtotal: parseFloat(d.subtotal)
            };
        });
    }
    renderOrder();
}

function renderOrder() {
    const cont = document.getElementById('order-items');
    const values = Object.values(items);

    if (values.length === 0) {
        cont.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-gray-600"><i class="fas fa-shopping-bag text-4xl mb-3"></i><p class="text-sm">Agrega productos</p></div>';
        document.getElementById('btn-cobrar').disabled = true;
        document.getElementById('subtotal').textContent = '$0.00';
        document.getElementById('impuesto').textContent = '$0.00';
        document.getElementById('total').textContent = '$0.00';
        return;
    }

    cont.innerHTML = values.map(item => `
        <div class="order-item flex items-center gap-2 py-2 px-1 rounded-lg border-b border-gray-800">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-white truncate">${item.nombre}</p>
                <p class="text-xs text-gray-400">$${item.precio.toFixed(2)} c/u</p>
            </div>
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button onclick="cambiarCantidad(${item.id}, ${item.cantidad - 1})" class="w-6 h-6 bg-gray-700 hover:bg-gray-600 rounded text-gray-300 text-sm leading-none">−</button>
                <span class="text-white text-sm w-5 text-center">${item.cantidad}</span>
                <button onclick="cambiarCantidad(${item.id}, ${item.cantidad + 1})" class="w-6 h-6 bg-gray-700 hover:bg-gray-600 rounded text-gray-300 text-sm leading-none">+</button>
            </div>
            <div class="text-right flex-shrink-0 w-16">
                <p class="text-sm font-medium text-white">$${item.subtotal.toFixed(2)}</p>
                <button onclick="eliminarItem(${item.id})" class="text-xs text-red-400 hover:text-red-300">quitar</button>
            </div>
        </div>
    `).join('');

    calcularTotales();
    document.getElementById('btn-cobrar').disabled = false;
}

function calcularTotales() {
    const subtotal = Object.values(items).reduce((s, i) => s + i.subtotal, 0);
    const descuento = parseFloat(document.getElementById('descuento-input').value) || 0;
    const base = subtotal - descuento;
    const impuesto = base * 0.07;
    const total = base + impuesto;
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('impuesto').textContent = '$' + impuesto.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

async function cambiarCantidad(detalleId, nuevaCantidad) {
    if (nuevaCantidad < 1) { eliminarItem(detalleId); return; }
    await fetch(`{{ url('/pos/detalle') }}/${detalleId}/cantidad`, {
        method: 'PATCH',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({cantidad: nuevaCantidad})
    });
    await recargarOrden();
}

async function eliminarItem(detalleId) {
    await fetch(`{{ url('/pos/detalle') }}/${detalleId}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    delete items[detalleId];
    await recargarOrden();
}

function limpiarOrden() {
    if (!confirm('¿Limpiar la orden actual?')) return;
    const promises = Object.keys(items).map(id => eliminarItem(parseInt(id)));
    Promise.all(promises).then(() => { items = {}; ventaId = null; renderOrder(); });
}

function abrirModalPago() {
    if (Object.keys(items).length === 0) return;
    const total = document.getElementById('total').textContent;
    document.getElementById('modal-total').textContent = total;
    document.getElementById('monto-recibido').value = '';
    seleccionarMetodo('efectivo', document.querySelector('[data-metodo="efectivo"]'));
    document.getElementById('modal-pago').classList.remove('hidden');
}

function cerrarModalPago() {
    document.getElementById('modal-pago').classList.add('hidden');
}

function seleccionarMetodo(metodo, btn) {
    document.querySelectorAll('.metodo-btn').forEach(b => {
        b.classList.remove('border-primary', 'bg-primary/10', 'text-white');
        b.classList.add('border-gray-700', 'text-gray-300');
    });
    if (btn) {
        btn.classList.add('border-primary', 'bg-primary/10', 'text-white');
        btn.classList.remove('border-gray-700', 'text-gray-300');
    }
    document.getElementById('metodo-seleccionado').value = metodo;
    document.getElementById('seccion-efectivo').style.display = metodo === 'efectivo' ? '' : 'none';
}

function calcularCambio() {
    const total = parseFloat(document.getElementById('total').textContent.replace('$','')) || 0;
    const recibido = parseFloat(document.getElementById('monto-recibido').value) || 0;
    const cambio = Math.max(0, recibido - total);
    document.getElementById('cambio-display').textContent = '$' + cambio.toFixed(2);
}

async function procesarPago() {
    if (!ventaId) { alert('No hay orden activa'); return; }
    const btn = document.querySelector('#modal-pago button[onclick="procesarPago()"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    const total = parseFloat(document.getElementById('total').textContent.replace('$',''));
    const descuento = parseFloat(document.getElementById('descuento-input').value) || 0;

    const body = {
        venta_id: ventaId,
        metodo_pago: document.getElementById('metodo-seleccionado').value,
        monto_recibido: parseFloat(document.getElementById('monto-recibido').value) || total,
        cliente_nombre: document.getElementById('cliente-nombre').value || null,
        cliente_ruc: document.getElementById('cliente-ruc').value || null,
    };

    const res = await fetch('{{ route("pos.cobrar") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(body)
    });
    const data = await res.json();

    if (data.success) {
        window.location.href = data.redirect;
    } else {
        alert('Error al procesar el pago');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Confirmar';
    }
}

// Init select metodo
document.addEventListener('DOMContentLoaded', () => {
    seleccionarMetodo('efectivo', document.querySelector('[data-metodo="efectivo"]'));
});
</script>
</body>
</html>
