<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = ['nombre', 'descripcion', 'icono', 'color', 'activo', 'orden'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function productosActivos()
    {
        return $this->hasMany(Producto::class)->where('activo', true);
    }
}
