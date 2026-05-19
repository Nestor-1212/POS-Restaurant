<?php

namespace Database\Seeders;

use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $mesas = [
            ['nombre' => 'Mesa 1', 'capacidad' => 4, 'ubicacion' => 'Interior'],
            ['nombre' => 'Mesa 2', 'capacidad' => 4, 'ubicacion' => 'Interior'],
            ['nombre' => 'Mesa 3', 'capacidad' => 4, 'ubicacion' => 'Interior'],
            ['nombre' => 'Mesa 4', 'capacidad' => 6, 'ubicacion' => 'Interior'],
            ['nombre' => 'Mesa 5', 'capacidad' => 6, 'ubicacion' => 'Interior'],
            ['nombre' => 'Mesa 6', 'capacidad' => 2, 'ubicacion' => 'Terraza'],
            ['nombre' => 'Mesa 7', 'capacidad' => 2, 'ubicacion' => 'Terraza'],
            ['nombre' => 'Mesa 8', 'capacidad' => 8, 'ubicacion' => 'Sala VIP'],
            ['nombre' => 'Barra 1', 'capacidad' => 2, 'ubicacion' => 'Barra'],
            ['nombre' => 'Barra 2', 'capacidad' => 2, 'ubicacion' => 'Barra'],
        ];

        foreach ($mesas as $mesa) {
            Mesa::firstOrCreate(['nombre' => $mesa['nombre']], array_merge($mesa, ['estado' => 'disponible']));
        }
    }
}
