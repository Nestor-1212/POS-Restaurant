<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Admin', 'descripcion' => 'Acceso completo al sistema'],
            ['nombre' => 'Cajero', 'descripcion' => 'Acceso al punto de venta'],
            ['nombre' => 'Cocina', 'descripcion' => 'Visualización y gestión de pedidos'],
            ['nombre' => 'Supervisor', 'descripcion' => 'Acceso a reportes y dashboard'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nombre' => $role['nombre']], $role);
        }
    }
}
