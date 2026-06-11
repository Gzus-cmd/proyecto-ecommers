<?php

namespace Tests\Unit;

use App\Models\Lote;
use Carbon\CarbonImmutable; // Regresamos al Immutable que usa tu equipo corporativo

uses(\Tests\TestCase::class);

test('el modelo lote convierte correctamente los tipos de datos (Casts)', function () {
    // 1. Arrange: Instancia en memoria con datos crudos
    $lote = new Lote([
        'fecha_vencimiento' => '2026-12-31',
        'cantidad_actual' => '50', 
        'costo_unitario' => '12.50'
    ]);

    // 2. Act & Assert: Validamos respetando la configuración real detectada
    
    // Cambiado a CarbonImmutable porque el sistema global de tu equipo lo exige
    expect($lote->fecha_vencimiento)->toBeInstanceOf(CarbonImmutable::class);
    
    expect($lote->cantidad_actual)->toBeInt()
        ->and($lote->cantidad_actual)->toBe(50);
        
    expect($lote->costo_unitario)->toBe('12.50');
});

test('un lote posee una relación funcional con su producto maestro', function () {
    $lote = new Lote();

    $relacion = $lote->producto();

    expect($relacion)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($relacion->getForeignKeyName())->toBe('producto_id');
});