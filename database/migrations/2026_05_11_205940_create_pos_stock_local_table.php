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
        Schema::create('pos_stock_local', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('pos_sedes');
            $table->foreignId('lote_local_id')->constrained('pos_lotes_local');
            $table->integer('cantidad_disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_stock_local');
    }
};
