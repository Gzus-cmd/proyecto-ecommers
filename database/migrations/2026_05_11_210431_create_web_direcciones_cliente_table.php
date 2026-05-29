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
        Schema::create('web_direcciones_cliente', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained('web_clientes')->onDelete('cascade');
        $table->string('alias')->nullable(); // Ej: Casa, Trabajo
        $table->string('direccion_completa');
        $table->string('referencia')->nullable();
        $table->string('distrito')->nullable();
        $table->string('ciudad')->nullable();
        $table->boolean('es_principal')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_direcciones_cliente');
    }
};
