<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Restaurant')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B35',
                        dark: '#1a1a2e',
                        darker: '#16213e',
                        card: '#0f3460',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #0d1117; }
        .sidebar { background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%); }
        .nav-item:hover, .nav-item.active { background: rgba(255,107,53,0.15); border-left: 3px solid #FF6B35; }
        .nav-item { border-left: 3px solid transparent; }
        .card { background: #161b22; border: 1px solid #30363d; }
        .stat-card { background: linear-gradient(135deg, #1a1a2e, #16213e); border: 1px solid #30363d; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; }
        ::-webkit-scrollbar-thumb { background: #FF6B35; border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body class="text-gray-100 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="sidebar w-64 min-h-screen flex flex-col fixed left-0 top-0 z-40">
        {{-- Logo --}}
        <div class="p-5 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                    <i class="fas fa-utensils text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-white text-sm leading-tight">POS Restaurant</p>
                    <p class="text-xs text-gray-400">Sistema de Ventas</p>
                </div>
            </div>
        </div>

        {{-- User Info --}}
        <div class="p-4 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-primary/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-primary text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-primary">{{ auth()->user()->role->nombre ?? 'Usuario' }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 overflow-y-auto">
            @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
            <div class="px-3 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">General</p>
                <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('dashboard') ? 'active text-white' : '' }}">
                    <i class="fas fa-chart-line w-4 text-primary"></i> Dashboard
                </a>
            </div>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isCajero())
            <div class="px-3 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1 mt-2">Caja</p>
                <a href="{{ route('pos.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('pos.*') ? 'active text-white' : '' }}">
                    <i class="fas fa-cash-register w-4 text-primary"></i> Punto de Venta
                </a>
            </div>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isCocina())
            <div class="px-3 mb-2">
                <a href="{{ route('cocina.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('cocina.*') ? 'active text-white' : '' }}">
                    <i class="fas fa-fire-burner w-4 text-orange-400"></i> Cocina
                </a>
            </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="px-3 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1 mt-2">Administración</p>
                <a href="{{ route('categorias.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('categorias.*') ? 'active text-white' : '' }}">
                    <i class="fas fa-tags w-4 text-primary"></i> Categorías
                </a>
                <a href="{{ route('productos.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('productos.*') ? 'active text-white' : '' }}">
                    <i class="fas fa-burger w-4 text-primary"></i> Productos
                </a>
                <a href="{{ route('mesas.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('mesas.*') ? 'active text-white' : '' }}">
                    <i class="fas fa-chair w-4 text-primary"></i> Mesas
                </a>
                <a href="{{ route('usuarios.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all {{ request()->routeIs('usuarios.*') ? 'active text-white' : '' }}">
                    <i class="fas fa-users w-4 text-primary"></i> Usuarios
                </a>
            </div>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
            <div class="px-3 mb-2">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1 mt-2">Reportes</p>
                <a href="{{ route('productos.inventario') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all">
                    <i class="fas fa-boxes-stacked w-4 text-primary"></i> Inventario
                </a>
                <a href="{{ route('reportes.ventas') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all">
                    <i class="fas fa-file-invoice-dollar w-4 text-primary"></i> Ventas
                </a>
                <a href="{{ route('reportes.historial') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all">
                    <i class="fas fa-receipt w-4 text-primary"></i> Historial
                </a>
                <a href="{{ route('reportes.stock-bajo') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all">
                    <i class="fas fa-triangle-exclamation w-4 text-yellow-400"></i> Stock Bajo
                </a>
                <a href="{{ route('reportes.cierres-caja') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:text-white transition-all">
                    <i class="fas fa-lock w-4 text-primary"></i> Cierres Caja
                </a>
            </div>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-gray-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-all">
                    <i class="fas fa-right-from-bracket w-4"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="ml-64 flex-1 min-h-screen">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 bg-gray-900/80 backdrop-blur border-b border-gray-800 px-6 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-400">
                <i class="fas fa-clock"></i>
                <span id="reloj">{{ now()->format('H:i') }}</span>
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        {{-- Alerts --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg">
                    <i class="fas fa-circle-exclamation mr-2"></i>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <div class="p-6">
            @yield('content')
        </div>
    </main>

    <script>
        function actualizarReloj() {
            const ahora = new Date();
            document.getElementById('reloj').textContent =
                ahora.toLocaleTimeString('es', {hour: '2-digit', minute: '2-digit'});
        }
        setInterval(actualizarReloj, 1000);
    </script>
    @stack('scripts')
</body>
</html>
