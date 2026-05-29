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
    Schema::create('central_lotes', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('producto_id')->constrained('central_productos_maestro');
        
        $table->string('numero_lote');
        $table->date('fecha_fabricacion')->nullable();
        $table->date('fecha_ingreso');
        $table->date('fecha_vencimiento');
        
        $table->integer('cantidad_inicial');
        $table->integer('cantidad_actual');
        
        $table->decimal('costo_unitario', 10, 2);
        
        $table->string('estado')->default('disponible'); // Ej: disponible, cuarentena, vencido
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('central_lotes');
    }
};
