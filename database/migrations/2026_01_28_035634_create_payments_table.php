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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Usuario que registró el pago');
            $table->decimal('amount', 12, 2)->comment('Monto del pago');
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'check'])->default('cash');
            $table->string('reference')->nullable()->comment('Número de referencia/cheque');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
