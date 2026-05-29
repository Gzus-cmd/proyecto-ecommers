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
        Schema::create('pos_metodos_pago', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); // Ej: Efectivo, Tarjeta, Yape, Plin
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_metodos_pago');
    }
};
