<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    const ADMIN      = 1;
    const CAJERO     = 2;
    const COCINA     = 3;
    const SUPERVISOR = 4;
}
