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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // VARCHAR(255) - Nombre de la categoría
            $table->text('description')->nullable();   // TEXT NULL - Descripción opcional
            $table->boolean('is_active')->default(true); // BOOLEAN DEFAULT 1 - Activa por defecto
            $table->timestamps();                      // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
