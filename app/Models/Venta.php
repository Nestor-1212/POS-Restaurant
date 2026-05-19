<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'numero_factura', 'mesa_id', 'usuario_id', 'cliente_nombre', 'cliente_ruc',
        'subtotal', 'descuento', 'impuesto', 'total', 'monto_recibido', 'cambio',
        'metodo_pago', 'detalle_pago', 'estado', 'notas', 'completada_at',
    ];

    protected function casts(): array
    {
        return [
            'detalle_pago' => 'array',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'total' => 'decimal:2',
            'monto_recibido' => 'decimal:2',
            'cambio' => 'decimal:2',
            'completada_at' => 'datetime',
        ];
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public static function generarNumeroFactura(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (intval(substr($ultimo->numero_factura, -6)) + 1) : 1;
        return 'FAC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
