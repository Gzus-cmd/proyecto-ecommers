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
        Schema::create('central_detalle_transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transferencia_id')->constrained('central_transferencias')->onDelete('cascade');
            $table->foreignId('lote_id')->constrained('central_lotes');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('central_detalle_transferencias');
    }
};
