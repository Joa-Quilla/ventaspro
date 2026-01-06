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
        Schema::table('products', function (Blueprint $table) {
            //Eliminar la foreing key eistente (cascade)
            $table->dropForeign(['category_id']);

            // Crear nueva foreing key con RESTRICT
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict'); // No permite borrar si tiene productos
        });
    }

    /**
     * Revertir a CASCADE (solo por si necesitas rollback)
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //Eliminar la foreing key eistente (restrict)
            $table->dropForeign(['category_id']);

            // Crear nueva foreing key con CASCADE
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade'); // Permite borrar y elimina productos relacionados
        });
    }
};
