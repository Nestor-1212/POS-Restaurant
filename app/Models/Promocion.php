<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';
    protected $fillable = [
        'producto_id', 'categoria_id', 'nombre', 'tipo',
        'descuento', 'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'descuento' => 'decimal:2',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function isVigente(): bool
    {
        $hoy = now()->toDateString();
        return $this->activo && $hoy >= $this->fecha_inicio && $hoy <= $this->fecha_fin;
    }
}
