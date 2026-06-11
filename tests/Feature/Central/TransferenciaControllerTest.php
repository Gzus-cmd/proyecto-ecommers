<?php

namespace Tests\Feature\Central;

use App\Models\User;
use App\Models\Sede;
use App\Models\Lote;
use App\Models\ProductoMaestro;
use App\Models\Transferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function crearEntornoLogisticoCompleto(): array {
    // 1. Sede Destino
    $sede = Sede::create([
        'codigo' => 'SED-TEST-01',
        'nombre' => 'Sede Trujillo Centro',
        'activo' => true
    ]);

    // 2. Categoria, Laboratorio y Proveedor obligatorios (Estructura real confirmada)
    $categoriaId = DB::table('central_categorias')->insertGetId([
        'nombre' => 'General QA', 'created_at' => now(), 'updated_at' => now()
    ]);
    
    $laboratorioId = DB::table('central_laboratorios')->insertGetId([
        'nombre' => 'Lab QA', 'created_at' => now(), 'updated_at' => now()
    ]);
    
    $proveedorId = DB::table('central_proveedores')->insertGetId([
        'razon_social' => 'Proveedor QA',
        'ruc'          => '20555666777', 
        'activo'       => true,
        'created_at'   => now(), 
        'updated_at'   => now()
    ]);

    // 3. Producto Maestro Activo
    $producto = ProductoMaestro::create([
        'sku' => 'SKU-LOG-99',
        'nombre_comercial' => 'Medicamento Controlado',
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedorId,
        'activo' => true
    ]);

    // 4. Lote con stock disponible
    $lote = Lote::create([
        'producto_id' => $producto->id,
        'numero_lote' => 'LOT-LOG-2026',
        'fecha_fabricacion' => '2026-01-01',
        'fecha_ingreso' => '2026-06-11',
        'fecha_vencimiento' => '2027-12-31',
        'cantidad_inicial' => 100,
        'cantidad_actual' => 100,
        'costo_unitario' => 5.00,
        'estado' => 'Disponible'
    ]);

    // 5. Tipo de movimiento por defecto para la auditoría (ID 1)
    if (!DB::table('central_tipos_movimiento')->where('id', 1)->exists()) {
        DB::table('central_tipos_movimiento')->insert([
            'id' => 1,
            'nombre' => 'Transferencia',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return [$sede, $lote];
}

test('un usuario autenticado puede listar las transferencias registradas', function () {
    $user = User::factory()->create();
    [$sede] = crearEntornoLogisticoCompleto();

    Transferencia::create([
        'sede_destino_id' => $sede->id,
        'fecha_envio' => '2026-06-11',
        'estado' => 'Pendiente'
    ]);

    $response = $this->actingAs($user)->get(route('central.transferencias.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Transferencias/Index')
        ->has('transferencias.data')
    );
});

test('un usuario puede almacenar una transferencia con multiples detalles', function () {
    $user = User::factory()->create();
    [$sede, $lote] = crearEntornoLogisticoCompleto();

    $payload = [
        'sede_destino_id' => $sede->id,
        'fecha_envio' => '2026-06-11',
        'observaciones' => 'Envío urgente de insumos',
        'detalles' => [
            [
                'lote_id' => $lote->id,
                'cantidad' => 30
            ]
        ]
    ];

    $response = $this->actingAs($user)->post(route('central.transferencias.store'), $payload);

    $response->assertRedirect(route('central.transferencias.index'));
    $this->assertDatabaseHas('central_transferencias', [
        'sede_destino_id' => $sede->id,
        'estado' => 'Pendiente'
    ]);
});

test('el metodo show devuelve json si es solicitado explicitamente por la vista', function () {
    $user = User::factory()->create();
    [$sede] = crearEntornoLogisticoCompleto();

    $transferencia = Transferencia::create([
        'sede_destino_id' => $sede->id,
        'fecha_envio' => '2026-06-11',
        'estado' => 'Pendiente'
    ]);

    $response = $this->actingAs($user)
                     ->get(route('central.transferencias.show', $transferencia->id), [
                         'HTTP_X-Requested-With' => 'XMLHttpRequest',
                         'Accept' => 'application/json'
                     ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['id', 'estado', 'sede_destino_id']);
});

test('el sistema procesa el envio descontando stock y generando auditoria', function () {
    $user = User::factory()->create();
    [$sede, $lote] = crearEntornoLogisticoCompleto();

    $transferencia = Transferencia::create([
        'sede_destino_id' => $sede->id,
        'fecha_envio' => '2026-06-11',
        'estado' => 'Pendiente'
    ]);

    $transferencia->detalles()->create([
        'lote_id' => $lote->id,
        'cantidad' => 40
    ]);

    // Apuntamos directamente al endpoint URL del controlador para evitar conflictos de nombres de ruta
    $response = $this->actingAs($user)->post("/central/transferencias/{$transferencia->id}/enviar");

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Enviado.');
    
    $this->assertEquals('Enviado', $transferencia->fresh()->estado);
    $this->assertEquals(60, $lote->fresh()->cantidad_actual);
    
    $this->assertDatabaseHas('central_movimientos_inventario', [
        'lote_id' => $lote->id,
        'movimentable_type' => Transferencia::class,
        'cantidad' => -40,
        'stock_antes' => 100,
        'stock_despues' => 60
    ]);
});

test('el proceso de envio falla y hace rollback si la cantidad solicitada supera el stock del lote', function () {
    $user = User::factory()->create();
    [$sede, $lote] = crearEntornoLogisticoCompleto();

    $transferencia = Transferencia::create([
        'sede_destino_id' => $sede->id,
        'fecha_envio' => '2026-06-11',
        'estado' => 'Pendiente'
    ]);

    $transferencia->detalles()->create([
        'lote_id' => $lote->id,
        'cantidad' => 150
    ]);

    // Apuntamos directamente al endpoint URL del controlador
    $response = $this->actingAs($user)->post("/central/transferencias/{$transferencia->id}/enviar");

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Sin stock.');
    
    $this->assertEquals('Pendiente', $transferencia->fresh()->estado);
    $this->assertEquals(100, $lote->fresh()->cantidad_actual);
});