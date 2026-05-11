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
        Schema::create('web_productos', function (Blueprint $table) {
        $table->string('sku')->primary(); 
        $table->string('nombre_comercial');
        $table->string('nombre_generico')->nullable();
        $table->text('descripcion')->nullable();
        $table->string('concentracion')->nullable();
        $table->string('forma_farmaceutica')->nullable();
        $table->decimal('precio_web', 10, 2);
        $table->boolean('requiere_receta')->default(false);
        $table->boolean('disponible')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_productos');
    }
};
