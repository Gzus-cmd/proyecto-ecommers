<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('central_movimientos_inventario', function (Blueprint $table) {
            $table->id();
            // Relación con el lote afectado
            $table->foreignId('lote_id')->constrained('central_lotes')->onDelete('cascade');
            
            // Relación con la tabla de tipos (Envío, Devolución, etc.)
            $table->foreignId('tipo_movimiento_id')->constrained('central_tipos_movimiento');
            
            // Relación Polimórfica: Crea 'movimentable_id' y 'movimentable_type'
            // Esto permite referenciar a una Transferencia, una Venta, etc.
            $table->morphs('movimentable'); 
            
            $table->integer('cantidad'); // Cantidad que varió (negativa para salidas)
            $table->integer('stock_antes'); // Auditoría: Stock antes del proceso
            $table->integer('stock_despues'); // Auditoría: Stock resultante
            
            $table->foreignId('usuario_id')->constrained('users'); // Quién hizo el movimiento
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('central_movimientos_inventario');
    }
};