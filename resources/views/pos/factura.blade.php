<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $venta->numero_factura }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #0d1117; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; }
            .ticket { width: 80mm; margin: 0 auto; font-family: monospace; }
        }
    </style>
</head>
<body class="min-h-screen flex items-start justify-center pt-8 p-4">
<div class="w-full max-w-sm">

    {{-- Actions --}}
    <div class="no-print flex gap-3 mb-4">
        <button onclick="window.print()" class="flex-1 bg-primary hover:bg-orange-600 text-white py-2.5 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors">
            <i class="fas fa-print"></i> Imprimir Ticket
        </button>
        <a href="{{ route('pos.index') }}" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2.5 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Nuevo Pedido
        </a>
    </div>

    {{-- Ticket --}}
    <div class="ticket bg-white text-gray-900 rounded-2xl p-6 no-print:shadow-2xl font-mono text-sm">
        {{-- Header --}}
        <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
            <p class="font-bold text-xl">RESTAURANTE</p>
            <p class="text-xs text-gray-500">Sistema POS</p>
            <p class="text-xs text-gray-500 mt-1">Tel: (000) 000-0000</p>
        </div>

        {{-- Info --}}
        <div class="space-y-1 text-xs mb-4 border-b border-dashed border-gray-300 pb-4">
            <div class="flex justify-between">
                <span class="text-gray-500">Factura:</span>
                <span class="font-bold">{{ $venta->numero_factura }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Fecha:</span>
                <span>{{ $venta->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Cajero:</span>
                <span>{{ $venta->usuario->name }}</span>
            </div>
            @if($venta->mesa)
            <div class="flex justify-between">
                <span class="text-gray-500">Mesa:</span>
                <span>{{ $venta->mesa->nombre }}</span>
            </div>
            @endif
            @if($venta->cliente_nombre)
            <div class="flex justify-between">
                <span class="text-gray-500">Cliente:</span>
                <span>{{ $venta->cliente_nombre }}</span>
            </div>
            @endif
            @if($venta->cliente_ruc)
            <div class="flex justify-between">
                <span class="text-gray-500">RUC:</span>
                <span>{{ $venta->cliente_ruc }}</span>
            </div>
            @endif
        </div>

        {{-- Items --}}
        <div class="mb-4 border-b border-dashed border-gray-300 pb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-2">
                <span>PRODUCTO</span>
                <div class="flex gap-6">
                    <span>CANT</span>
                    <span>PRECIO</span>
                    <span>TOTAL</span>
                </div>
            </div>
            @foreach($venta->detalles as $d)
            <div class="flex justify-between text-xs mb-1">
                <span class="flex-1 pr-2">{{ $d->producto->nombre }}{{ $d->variante ? ' (' . $d->variante->nombre . ')' : '' }}</span>
                <div class="flex gap-4 flex-shrink-0">
                    <span class="w-6 text-center">{{ $d->cantidad }}</span>
                    <span class="w-12 text-right">${{ number_format($d->precio_unitario, 2) }}</span>
                    <span class="w-12 text-right font-medium">${{ number_format($d->subtotal, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="space-y-1 text-xs mb-4">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>${{ number_format($venta->subtotal, 2) }}</span>
            </div>
            @if($venta->descuento > 0)
            <div class="flex justify-between text-green-600">
                <span>Descuento:</span>
                <span>-${{ number_format($venta->descuento, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span>ITBMS (7%):</span>
                <span>${{ number_format($venta->impuesto, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-base border-t border-gray-300 pt-2 mt-2">
                <span>TOTAL:</span>
                <span>${{ number_format($venta->total, 2) }}</span>
            </div>
        </div>

        {{-- Payment --}}
        <div class="border-t border-dashed border-gray-300 pt-4 space-y-1 text-xs">
            <div class="flex justify-between">
                <span class="text-gray-500">Método:</span>
                <span class="capitalize font-medium">{{ $venta->metodo_pago }}</span>
            </div>
            @if($venta->monto_recibido)
            <div class="flex justify-between">
                <span class="text-gray-500">Recibido:</span>
                <span>${{ number_format($venta->monto_recibido, 2) }}</span>
            </div>
            <div class="flex justify-between font-medium text-green-600">
                <span>Cambio:</span>
                <span>${{ number_format($venta->cambio, 2) }}</span>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="text-center mt-6 pt-4 border-t border-dashed border-gray-300">
            <p class="text-xs text-gray-400">¡Gracias por su visita!</p>
            <p class="text-xs text-gray-400">Vuelva pronto</p>
        </div>
    </div>
</div>
<script>
    // Auto print on load if needed
    // window.print();
</script>
</body>
</html>
