<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CocinaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas
Route::middleware('auth')->group(function () {

    // Dashboard - Admin y Supervisor
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin,supervisor');

    // Categorías - solo Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class);
        Route::patch('categorias/{categoria}/toggle', [CategoriaController::class, 'toggleEstado'])
            ->name('categorias.toggle');
    });

    // Productos - Admin y Supervisor
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggleEstado'])
            ->name('productos.toggle');
        Route::get('inventario', [ProductoController::class, 'inventario'])
            ->name('productos.inventario');
        Route::post('productos/{producto}/ajuste-stock', [ProductoController::class, 'ajusteStock'])
            ->name('productos.ajuste-stock');
    });

    // Mesas - Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('mesas', MesaController::class);
    });

    // POS / Cajero - Admin y Cajero
    Route::middleware('role:admin,cajero')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/orden/{mesa?}', [PosController::class, 'orden'])->name('pos.orden');
        Route::post('/pos/agregar-producto', [PosController::class, 'agregarProducto'])->name('pos.agregar');
        Route::delete('/pos/detalle/{detalle}', [PosController::class, 'quitarProducto'])->name('pos.quitar');
        Route::patch('/pos/detalle/{detalle}/cantidad', [PosController::class, 'actualizarCantidad'])->name('pos.cantidad');
        Route::post('/pos/cobrar', [PosController::class, 'cobrar'])->name('pos.cobrar');
        Route::get('/pos/factura/{venta}', [PosController::class, 'factura'])->name('pos.factura');
        Route::get('/pos/venta/{mesaId?}', [PosController::class, 'obtenerVenta'])->name('pos.venta');
    });

    // Cocina
    Route::middleware('role:admin,cocina,supervisor')->group(function () {
        Route::get('/cocina', [CocinaController::class, 'index'])->name('cocina.index');
        Route::patch('/cocina/venta/{venta}/preparacion', [CocinaController::class, 'marcarEnPreparacion'])->name('cocina.preparacion');
        Route::patch('/cocina/venta/{venta}/listo', [CocinaController::class, 'marcarListo'])->name('cocina.listo');
        Route::patch('/cocina/detalle/{detalle}/preparado', [CocinaController::class, 'marcarDetallePreparado'])->name('cocina.detalle-preparado');
        Route::get('/cocina/pedidos', [CocinaController::class, 'pedidosJson'])->name('cocina.pedidos-json');
    });

    // Reportes - Admin y Supervisor
    Route::middleware('role:admin,supervisor')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/ventas', [ReporteController::class, 'ventas'])->name('ventas');
        Route::get('/productos', [ReporteController::class, 'productos'])->name('productos');
        Route::get('/cierres-caja', [ReporteController::class, 'cierresCaja'])->name('cierres-caja');
        Route::get('/historial', [ReporteController::class, 'historialFacturas'])->name('historial');
        Route::get('/stock-bajo', [ReporteController::class, 'stockBajo'])->name('stock-bajo');
        Route::post('/abrir-caja', [ReporteController::class, 'abrirCaja'])->name('abrir-caja');
        Route::patch('/cerrar-caja/{cierreCaja}', [ReporteController::class, 'cerrarCaja'])->name('cerrar-caja');
    });

    // Usuarios - solo Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('usuarios', UsuarioController::class);
        Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleActivo'])
            ->name('usuarios.toggle');
    });
});
