<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = ['nombre', 'capacidad', 'ubicacion', 'estado'];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function ventaActiva()
    {
        return $this->hasOne(Venta::class)->whereIn('estado', ['pendiente', 'en_preparacion', 'listo']);
    }

    public function isDisponible(): bool
    {
        return $this->estado === 'disponible';
    }
}
