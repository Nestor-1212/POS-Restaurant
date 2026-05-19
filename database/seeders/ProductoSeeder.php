<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Bebidas
            ['cat' => 'Bebidas', 'nombre' => 'Coca Cola', 'precio' => 1.50, 'stock' => 100],
            ['cat' => 'Bebidas', 'nombre' => 'Agua Purificada', 'precio' => 0.75, 'stock' => 80],
            ['cat' => 'Bebidas', 'nombre' => 'Jugo Natural', 'precio' => 2.00, 'stock' => 50],
            ['cat' => 'Bebidas', 'nombre' => 'Café', 'precio' => 1.25, 'stock' => 60, 'variantes' => [
                ['nombre' => 'Pequeño', 'precio' => 1.00],
                ['nombre' => 'Mediano', 'precio' => 1.25],
                ['nombre' => 'Grande', 'precio' => 1.75],
            ]],
            // Arroces
            ['cat' => 'Arroces', 'nombre' => 'Arroz con Pollo', 'precio' => 5.00, 'stock' => 30, 'tiempo' => 15],
            ['cat' => 'Arroces', 'nombre' => 'Arroz con Mariscos', 'precio' => 7.50, 'stock' => 20, 'tiempo' => 20],
            ['cat' => 'Arroces', 'nombre' => 'Arroz con Carne', 'precio' => 5.50, 'stock' => 25, 'tiempo' => 15],
            // Sopas
            ['cat' => 'Sopas', 'nombre' => 'Sopa de Carne', 'precio' => 4.25, 'stock' => 20, 'tiempo' => 10],
            ['cat' => 'Sopas', 'nombre' => 'Sopa de Mariscos', 'precio' => 6.00, 'stock' => 15, 'tiempo' => 12],
            ['cat' => 'Sopas', 'nombre' => 'Sancocho', 'precio' => 5.00, 'stock' => 20, 'tiempo' => 10],
            // Carnes
            ['cat' => 'Carnes', 'nombre' => 'Bistec a la Plancha', 'precio' => 8.50, 'stock' => 20, 'tiempo' => 20],
            ['cat' => 'Carnes', 'nombre' => 'Pollo Frito', 'precio' => 6.00, 'stock' => 25, 'tiempo' => 15],
            ['cat' => 'Carnes', 'nombre' => 'Chuleta Ahumada', 'precio' => 7.00, 'stock' => 15, 'tiempo' => 18],
            // Hamburguesas
            ['cat' => 'Hamburguesas', 'nombre' => 'Hamburguesa Clásica', 'precio' => 4.50, 'stock' => 30, 'tiempo' => 12],
            ['cat' => 'Hamburguesas', 'nombre' => 'Hamburguesa Doble', 'precio' => 6.50, 'stock' => 25, 'tiempo' => 15],
            ['cat' => 'Hamburguesas', 'nombre' => 'Hamburguesa BBQ', 'precio' => 5.50, 'stock' => 20, 'tiempo' => 12],
            // Postres
            ['cat' => 'Postres', 'nombre' => 'Flan', 'precio' => 2.50, 'stock' => 15],
            ['cat' => 'Postres', 'nombre' => 'Pastel de Chocolate', 'precio' => 3.00, 'stock' => 10],
            ['cat' => 'Postres', 'nombre' => 'Helado', 'precio' => 2.00, 'stock' => 20],
            // Extras
            ['cat' => 'Extras', 'nombre' => 'Papas Fritas', 'precio' => 1.50, 'stock' => 50, 'tiempo' => 8],
            ['cat' => 'Extras', 'nombre' => 'Ensalada', 'precio' => 2.00, 'stock' => 30],
            ['cat' => 'Extras', 'nombre' => 'Pan con Mantequilla', 'precio' => 0.75, 'stock' => 40],
        ];

        foreach ($productos as $p) {
            $cat = Categoria::where('nombre', $p['cat'])->first();
            if (!$cat) continue;

            $producto = Producto::firstOrCreate(['nombre' => $p['nombre'], 'categoria_id' => $cat->id], [
                'categoria_id' => $cat->id,
                'precio' => $p['precio'],
                'impuesto' => 7,
                'stock' => $p['stock'],
                'stock_minimo' => 5,
                'tiempo_preparacion' => $p['tiempo'] ?? 0,
                'tiene_variantes' => isset($p['variantes']),
                'activo' => true,
            ]);

            if (isset($p['variantes'])) {
                foreach ($p['variantes'] as $v) {
                    $producto->variantes()->firstOrCreate(['nombre' => $v['nombre']], [
                        'precio' => $v['precio'],
                        'stock' => 50,
                        'activo' => true,
                    ]);
                }
            }
        }
    }
}
