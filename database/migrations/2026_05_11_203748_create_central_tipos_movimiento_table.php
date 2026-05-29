<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_tipos_movimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); 
            $table->timestamps();
        });

        // Insertamos los datos necesarios para que el sistema funcione de inmediato
        DB::table('central_tipos_movimiento')->insert([
            ['nombre' => 'Envío por Transferencia', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Devolución por Transferencia', 'created_at' => now(), 'updated_at' => now()],
            // Puedes agregar más aquí en el futuro (Venta, Compra, etc.)
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('central_tipos_movimiento');
    }
};