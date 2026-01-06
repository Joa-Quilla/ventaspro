<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de items/productos vendidos en cada venta
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            // Relación con la venta
            $table->foreignId('sale_id')
                ->constrained()
                ->cascadeOnDelete();  // Si se borra la venta, borrar sus items

            // Relación con el producto
            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();  // No borrar productos con ventas

            // Información al momento de la venta (precio histórico)
            $table->integer('quantity');           // Cantidad vendida
            $table->decimal('unit_price', 10, 2);  // Precio unitario (al momento de venta)
            $table->decimal('subtotal', 10, 2);    // quantity * unit_price

            $table->timestamps();

            // Índices
            $table->index('sale_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
