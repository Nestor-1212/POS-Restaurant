<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['role_id', 'name', 'email', 'telefono', 'password', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function cierresCaja()
    {
        return $this->hasMany(CierreCaja::class);
    }

    public function isAdmin(): bool
    {
        return $this->role_id === Role::ADMIN;
    }

    public function isCajero(): bool
    {
        return $this->role_id === Role::CAJERO;
    }

    public function isCocina(): bool
    {
        return $this->role_id === Role::COCINA;
    }

    public function isSupervisor(): bool
    {
        return $this->role_id === Role::SUPERVISOR;
    }

    public function hasRole(string $role): bool
    {
        return strtolower($this->role->nombre) === strtolower($role);
    }
}
