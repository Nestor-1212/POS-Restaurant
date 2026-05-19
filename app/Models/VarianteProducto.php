<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarianteProducto extends Model
{
    protected $table = 'variantes_producto';
    protected $fillable = ['producto_id', 'nombre', 'precio', 'stock', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'precio' => 'decimal:2'];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
