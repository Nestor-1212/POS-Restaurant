<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — POS Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0d1117 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md px-6">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-500 rounded-2xl mb-4 shadow-lg shadow-orange-500/30">
                <i class="fas fa-utensils text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">POS Restaurant</h1>
            <p class="text-gray-400 text-sm mt-1">Sistema de Punto de Venta</p>
        </div>

        {{-- Form --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-semibold text-white mb-6">Iniciar Sesión</h2>

            @if($errors->any())
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Correo electrónico</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-3 text-gray-500 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition"
                            placeholder="admin@restaurante.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Contraseña</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-3 text-gray-500 text-sm"></i>
                        <input type="password" name="password" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition"
                            placeholder="••••••••">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="accent-orange-500">
                    <label for="remember" class="text-sm text-gray-400">Recordarme</label>
                </div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-right-to-bracket"></i> Ingresar
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-600 mt-6">POS Restaurant v1.0 &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
