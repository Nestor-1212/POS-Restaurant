<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'descripcion', 'precio', 'impuesto',
        'stock', 'stock_minimo', 'imagen', 'codigo_barra', 'codigo_qr',
        'tiempo_preparacion', 'tiene_variantes', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'tiene_variantes' => 'boolean',
            'precio' => 'decimal:2',
            'impuesto' => 'decimal:2',
        ];
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProducto::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getPrecioConImpuestoAttribute(): float
    {
        return $this->precio * (1 + $this->impuesto / 100);
    }
}
