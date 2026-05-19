<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->decimal('impuesto', 5, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('imagen')->nullable();
            $table->string('codigo_barra', 50)->nullable()->unique();
            $table->string('codigo_qr', 100)->nullable();
            $table->integer('tiempo_preparacion')->default(0)->comment('minutos');
            $table->boolean('tiene_variantes')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('variantes_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('nombre', 50);
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('cascade');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->string('tipo')->default('porcentaje')->comment('porcentaje, monto_fijo');
            $table->decimal('descuento', 10, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promociones');
        Schema::dropIfExists('variantes_producto');
        Schema::dropIfExists('productos');
    }
};
