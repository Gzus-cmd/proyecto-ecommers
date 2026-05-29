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
        Schema::create('web_pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained('web_clientes');
        // nullable() porque puede ser delivery (no requiere sede de recojo)
        $table->foreignId('sede_recojo_id')->nullable()->constrained('web_sedes');
        // nullable() porque puede ser recojo en tienda (no requiere dirección de envío)
        $table->foreignId('direccion_envio_id')->nullable()->constrained('web_direcciones_cliente');
        
        $table->timestamp('fecha_pedido')->useCurrent();
        $table->foreignId('estado_id')->constrained('web_estados_pedido');
        
        $table->decimal('subtotal', 10, 2);
        $table->decimal('impuesto', 10, 2);
        $table->decimal('total', 10, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_pedidos');
    }
};
