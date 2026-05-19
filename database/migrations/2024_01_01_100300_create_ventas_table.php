<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura', 20)->unique();
            $table->foreignId('mesa_id')->nullable()->constrained('mesas')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->string('cliente_nombre', 150)->nullable();
            $table->string('cliente_ruc', 30)->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('monto_recibido', 10, 2)->nullable();
            $table->decimal('cambio', 10, 2)->default(0);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'yappy', 'ach', 'transferencia', 'mixto'])->default('efectivo');
            $table->json('detalle_pago')->nullable()->comment('para pago mixto');
            $table->enum('estado', ['pendiente', 'en_preparacion', 'listo', 'completada', 'cancelada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamp('completada_at')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('variante_id')->nullable()->constrained('variantes_producto')->onDelete('set null');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'en_preparacion', 'listo'])->default('pendiente');
            $table->timestamps();
        });

        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->decimal('saldo_inicial', 10, 2)->default(0);
            $table->decimal('total_efectivo', 10, 2)->default(0);
            $table->decimal('total_tarjeta', 10, 2)->default(0);
            $table->decimal('total_yappy', 10, 2)->default(0);
            $table->decimal('total_otros', 10, 2)->default(0);
            $table->decimal('total_ventas', 10, 2)->default(0);
            $table->integer('num_ventas')->default(0);
            $table->decimal('saldo_final', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamp('apertura_at');
            $table->timestamp('cierre_at')->nullable();
            $table->timestamps();
        });

        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste'])->default('ajuste');
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('motivo')->nullable();
            $table->timestamps();
        });

        Schema::create('configuracion_impresoras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->enum('tipo', ['usb', 'red', 'pdf'])->default('usb');
            $table->string('ip', 45)->nullable();
            $table->integer('puerto')->nullable();
            $table->enum('ancho_papel', ['58mm', '80mm'])->default('80mm');
            $table->string('nombre_restaurante', 150)->nullable();
            $table->string('ruc_nit', 30)->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->text('mensaje_pie')->nullable();
            $table->boolean('imprimir_logo')->default(false);
            $table->boolean('activo')->default(true);
            $table->boolean('predeterminada')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_impresoras');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('cierres_caja');
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
    }
};
