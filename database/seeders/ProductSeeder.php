<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categoría de prueba
        $category = Category::firstOrCreate(
            ['name' => 'Medicamentos'],
            ['description' => 'Productos farmacéuticos', 'is_active' => true]
        );

        // Productos con diferentes niveles de stock
        $products = [
            [
                'name' => 'Aspirina 500mg',
                'sku' => 'ASP-500',
                'barcode' => '7501234567890',
                'description' => 'Analgésico y antipirético',
                'category_id' => $category->id,
                'price' => 15.50,
                'cost' => 10.00,
                'stock' => 50,
                'min_stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Paracetamol 500mg',
                'sku' => 'PAR-500',
                'barcode' => '7501234567891',
                'description' => 'Analgésico y antipirético',
                'category_id' => $category->id,
                'price' => 12.00,
                'cost' => 8.00,
                'stock' => 5, // Stock bajo para probar
                'min_stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Ibuprofeno 400mg',
                'sku' => 'IBU-400',
                'barcode' => '7501234567892',
                'description' => 'Antiinflamatorio',
                'category_id' => $category->id,
                'price' => 18.00,
                'cost' => 12.00,
                'stock' => 100,
                'min_stock' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        $this->command->info('✅ Productos de prueba creados exitosamente');
    }
}
