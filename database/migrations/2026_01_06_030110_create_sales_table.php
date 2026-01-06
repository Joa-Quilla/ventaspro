<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de ventas (transacciones del POS)
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Relación con el usuario (cajero que realizó la venta)
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();  // No borrar usuarios con ventas

            // Información del cliente
            $table->string('customer_name')->nullable();  // Opcional: nombre del cliente
            $table->string('customer_phone')->nullable(); // Opcional: teléfono

            // Totales
            $table->decimal('subtotal', 10, 2);  // Suma de productos
            $table->decimal('tax', 10, 2)->default(0);  // IVA (12% en Guatemala)
            $table->decimal('total', 10, 2);     // Total a pagar

            // Método de pago
            $table->enum('payment_method', ['cash', 'card', 'transfer'])
                ->default('cash');  // efectivo, tarjeta, transferencia

            // Estado de la venta
            $table->enum('status', ['completed', 'cancelled'])
                ->default('completed');

            // Notas adicionales
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
