<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Información básica del producto
            $table->string('name');                     // Nombre del producto
            $table->string('sku')->unique();            // Código único (Stock Keeping Unit)
            $table->string('barcode')->nullable()->unique(); // Código de barras (opcional pero único)
            $table->text('description')->nullable();    // Descripción del producto
            
            // Relación con categorías (FOREIGN KEY)
            $table->foreignId('category_id')            // Crea columna category_id BIGINT UNSIGNED
                  ->constrained()                       // Crea FOREIGN KEY a tabla 'categories'
                  ->cascadeOnDelete();                  // Si se borra la categoría, borra los productos
            
            // Precios e inventario
            $table->decimal('price', 10, 2);            // Precio de venta (10 dígitos, 2 decimales)
            $table->decimal('cost', 10, 2)->default(0); // Costo de compra
            $table->integer('stock')->default(0);       // Cantidad en inventario
            $table->integer('min_stock')->default(0);   // Stock mínimo para alertas
            
            // Otros campos
            $table->boolean('is_active')->default(true); // Producto activo
            $table->string('image')->nullable();         // Ruta de la imagen
            
            $table->timestamps();                        // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
