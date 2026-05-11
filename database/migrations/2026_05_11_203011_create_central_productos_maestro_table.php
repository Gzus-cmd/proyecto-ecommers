<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('central_productos_maestro', function (Blueprint $table) {
        $table->id();
        $table->string('sku')->unique();
        $table->string('nombre_comercial');
        $table->string('nombre_generico')->nullable();
        $table->text('descripcion')->nullable();        
        $table->foreignId('categoria_id')->constrained('central_categorias');
        $table->foreignId('laboratorio_id')->constrained('central_laboratorios');
        $table->foreignId('proveedor_id')->constrained('central_proveedores');        
        $table->boolean('requiere_receta')->default(false);
        $table->string('registro_sanitario')->nullable();
        $table->string('concentracion')->nullable();
        $table->string('forma_farmaceutica')->nullable();
        $table->string('unidad_medida')->nullable();
        $table->integer('stock_minimo')->default(0);
        $table->boolean('activo')->default(true);
        
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('central_productos_maestro');
    }
};
