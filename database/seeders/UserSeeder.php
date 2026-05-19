<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrador', 'email' => 'admin@restaurante.com', 'role_id' => 1, 'password' => 'admin123'],
            ['name' => 'María Cajero', 'email' => 'cajero@restaurante.com', 'role_id' => 2, 'password' => 'cajero123'],
            ['name' => 'Juan Cocina', 'email' => 'cocina@restaurante.com', 'role_id' => 3, 'password' => 'cocina123'],
            ['name' => 'Pedro Supervisor', 'email' => 'supervisor@restaurante.com', 'role_id' => 4, 'password' => 'super123'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], [
                'name' => $u['name'],
                'role_id' => $u['role_id'],
                'password' => Hash::make($u['password']),
                'activo' => true,
            ]);
        }
    }
}
