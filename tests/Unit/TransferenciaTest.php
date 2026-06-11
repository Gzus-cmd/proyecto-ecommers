<?php

use App\Models\Transferencia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

// Cargamos el entorno de Laravel para que resuelva las relaciones de los modelos
uses(Tests\TestCase::class);

test('el modelo transferencia posee las relaciones estructurales correctas', function () {
    $transferencia = new Transferencia();

    // 1. Validar la relación hacia la Sede de destino (Punto de venta)
    $relacionSede = $transferencia->sedeDestino();
    expect($relacionSede)->toBeInstanceOf(BelongsTo::class)
        ->and($relacionSede->getForeignKeyName())->toBe('sede_destino_id');

    // 2. Validar la relación hacia los medicamentos detallados en el envío
    $relacionDetalles = $transferencia->detalles();
    expect($relacionDetalles)->toBeInstanceOf(HasMany::class)
        ->and($relacionDetalles->getForeignKeyName())->toBe('transferencia_id');
});

test('el modelo transferencia implementa la relacion polimorfica inversa hacia movimientos', function () {
    $transferencia = new Transferencia();

    // 3. Validar que se puedan extraer sus movimientos asociados de auditoría
    $relacionMovimientos = $transferencia->movimientos();
    
    expect($relacionMovimientos)->toBeInstanceOf(MorphMany::class)
        ->and($relacionMovimientos->getMorphType())->toBe('movimentable_type')
        ->and($relacionMovimientos->getForeignKeyName())->toBe('movimentable_id');
});