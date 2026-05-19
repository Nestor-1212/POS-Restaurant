<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Bebidas', 'icono' => 'glass-water', 'color' => '#3B82F6', 'orden' => 1],
            ['nombre' => 'Arroces', 'icono' => 'bowl-rice', 'color' => '#F59E0B', 'orden' => 2],
            ['nombre' => 'Sopas', 'icono' => 'bowl-food', 'color' => '#EF4444', 'orden' => 3],
            ['nombre' => 'Carnes', 'icono' => 'drumstick-bite', 'color' => '#DC2626', 'orden' => 4],
            ['nombre' => 'Hamburguesas', 'icono' => 'burger', 'color' => '#FF6B35', 'orden' => 5],
            ['nombre' => 'Pizzas', 'icono' => 'pizza-slice', 'color' => '#EC4899', 'orden' => 6],
            ['nombre' => 'Postres', 'icono' => 'cake-candles', 'color' => '#A855F7', 'orden' => 7],
            ['nombre' => 'Extras', 'icono' => 'star', 'color' => '#10B981', 'orden' => 8],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['nombre' => $cat['nombre']], array_merge($cat, ['activo' => true]));
        }
    }
}
