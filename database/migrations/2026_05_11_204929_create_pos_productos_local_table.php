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
        Schema::create('pos_productos_local', function (Blueprint $table) {
            $table->string('sku')->primary(); 
            
            $table->string('nombre_comercial');
            $table->string('nombre_generico')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('concentracion')->nullable();
            $table->string('forma_farmaceutica')->nullable();
            $table->boolean('requiere_receta')->default(false);
            
            $table->decimal('precio_venta', 10, 2); 
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_productos_local');
    }
};
