<?php

use App\Models\DetalleTransferencia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Le indicamos a Pest que cargue el entorno de Laravel para las relaciones de Eloquent
uses(Tests\TestCase::class);

test('el modelo detalle transferencia gestiona correctamente sus atributos', function () {
    // 1. Arrange: Creamos una instancia en memoria
    $detalle = new DetalleTransferencia([
        'cantidad' => '150' // Pasamos como string para validar su coherencia numérica
    ]);

    // 2. Act & Assert: Comprobamos que el dato pueda ser tratado como entero
    expect((int) $detalle->cantidad)->toBeInt()->toBe(150);
});

test('el modelo detalle transferencia posee las relaciones de pertenencia correctas', function () {
    $detalle = new DetalleTransferencia();

    // Validar la relación con la Transferencia cabecera
    $relacionTransferencia = $detalle->transferencia();
    expect($relacionTransferencia)->toBeInstanceOf(BelongsTo::class)
        ->and($relacionTransferencia->getForeignKeyName())->toBe('transferencia_id');

    // Validar la relación con el Lote del medicamento
    $relacionLote = $detalle->lote();
    expect($relacionLote)->toBeInstanceOf(BelongsTo::class)
        ->and($relacionLote->getForeignKeyName())->toBe('lote_id');
});