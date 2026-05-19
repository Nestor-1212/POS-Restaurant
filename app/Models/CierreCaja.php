<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierreCaja extends Model
{
    protected $table = 'cierres_caja';
    protected $fillable = [
        'usuario_id', 'saldo_inicial', 'total_efectivo', 'total_tarjeta',
        'total_yappy', 'total_otros', 'total_ventas', 'num_ventas',
        'saldo_final', 'observaciones', 'apertura_at', 'cierre_at',
    ];

    protected function casts(): array
    {
        return [
            'apertura_at' => 'datetime',
            'cierre_at' => 'datetime',
            'saldo_inicial' => 'decimal:2',
            'total_efectivo' => 'decimal:2',
            'total_tarjeta' => 'decimal:2',
            'total_yappy' => 'decimal:2',
            'total_otros' => 'decimal:2',
            'total_ventas' => 'decimal:2',
            'saldo_final' => 'decimal:2',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
