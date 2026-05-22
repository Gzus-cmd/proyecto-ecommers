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
        Schema::create('central_transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_destino_id')->constrained('central_sedes');
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_recepcion')->nullable();
            $table->foreignId('estado_id')->constrained('central_estados_transferencia');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('central_transferencias');
    }
};
