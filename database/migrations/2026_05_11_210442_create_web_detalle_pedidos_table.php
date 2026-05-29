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
        Schema::create('web_detalle_pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pedido_id')->constrained('web_pedidos')->onDelete('cascade');
        
        // Relación manual hacia el SKU (string)
        $table->string('sku_producto');
        $table->foreign('sku_producto')->references('sku')->on('web_productos');
        
        $table->integer('cantidad');
        $table->decimal('precio_unitario', 10, 2);
        $table->decimal('subtotal', 10, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_detalle_pedidos');
    }
};
