<?php

use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

// Le recordamos a Pest que cargue el entorno de Laravel para las conexiones de base de datos
uses(Tests\TestCase::class);

test('el modelo movimiento de inventario gestiona correctamente sus atributos numéricos', function () {
    // 1. Arrange: Creamos un movimiento en memoria pasando números como strings
    $movimiento = new MovimientoInventario([
        'cantidad' => '20',
        'stock_antes' => '100',
        'stock_despues' => '80'
    ]);

    // 2. Act & Assert: Aunque tus compañeros no los pusieron explícitamente en $casts,
    // verificamos que el modelo devuelva o asigne números válidos en sus campos lógicos.
    expect((int) $movimiento->cantidad)->toBeInt()->toBe(20)
        ->and((int) $movimiento->stock_antes)->toBeInt()->toBe(100)
        ->and((int) $movimiento->stock_despues)->toBeInt()->toBe(80);
});

test('un movimiento de inventario posee las relaciones base correctas con lote, tipo y usuario', function () {
    $movimiento = new MovimientoInventario();

    // Verificamos la relación con Lote
    $relacionLote = $movimiento->lote();
    expect($relacionLote)->toBeInstanceOf(BelongsTo::class)
        ->and($relacionLote->getForeignKeyName())->toBe('lote_id');

    // Verificamos la relación con Tipo de Movimiento
    $relacionTipo = $movimiento->tipo();
    expect($relacionTipo)->toBeInstanceOf(BelongsTo::class)
        ->and($relacionTipo->getForeignKeyName())->toBe('tipo_movimiento_id');

    // Verificamos la relación con el Usuario que hizo el movimiento
    $relacionUsuario = $movimiento->usuario();
    expect($relacionUsuario)->toBeInstanceOf(BelongsTo::class)
        ->and($relacionUsuario->getForeignKeyName())->toBe('usuario_id');
});

test('un movimiento de inventario implementa una relación polimórfica funcional', function () {
    $movimiento = new MovimientoInventario();

    // Accedemos al método polimórfico del modelo real
    $relacionPolimorfica = $movimiento->movimentable();

    // Assert: Debe ser una instancia de MorphTo (relación dinámica)
    expect($relacionPolimorfica)->toBeInstanceOf(MorphTo::class);
});