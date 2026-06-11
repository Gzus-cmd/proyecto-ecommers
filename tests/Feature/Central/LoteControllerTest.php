<?php

namespace Tests\Feature\Central;

use App\Models\Lote;
use App\Models\User;
use App\Models\ProductoMaestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function crearDependenciasProducto(): array {
    // 1. Resolver Categoría
    $categoriaId = DB::table('central_categorias')->insertGetId([
        'nombre' => 'Test Categoria',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 2. Resolver Laboratorio
    $laboratorioId = DB::table('central_laboratorios')->insertGetId([
        'nombre' => 'Test Laboratorio',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. Resolver Proveedor (Incluye validación dinámica de nombre y campo RUC obligatorio)
    $columnaProveedor = Schema::hasColumn('central_proveedores', 'nombre') ? 'nombre' : 'razon_social';
    
    $datosProveedor = [
        $columnaProveedor => 'Test Proveedor',
        'created_at'      => now(),
        'updated_at'      => now(),
    ];

    // Agregamos el RUC obligatorio que encontramos en el análisis de QA
    if (Schema::hasColumn('central_proveedores', 'ruc')) {
        $datosProveedor['ruc'] = '20123456789';
    }

    $proveedorId = DB::table('central_proveedores')->insertGetId($datosProveedor);

    return [$categoriaId, $laboratorioId, $proveedorId];
}

test('un usuario autenticado puede listar los lotes de medicamentos', function () {
    $user = User::factory()->create();
    
    // Intentamos usar la Factory del producto Estándar para DevOps
    try {
        $producto = ProductoMaestro::factory()->create();
    } catch (\Throwable $e) {
        // Fallback si la Factory está rota o incompleta
        [$categoriaId, $laboratorioId, $proveedorId] = crearDependenciasProducto();
        
        $producto = ProductoMaestro::create([
            'categoria_id'   => $categoriaId,
            'laboratorio_id' => $laboratorioId,
            'proveedor_id'   => $proveedorId,
            'nombre_comercial' => 'Paracetamol',
            'sku'             => 'PROD-QA-01',
            'activo'          => true
        ]);
    }

    // Insertamos el Lote respetando las columnas exactas de la BD
    Lote::create([
        'producto_id'       => $producto->id,
        'numero_lote'       => 'LOT-2026-A',
        'fecha_fabricacion' => '2026-01-01',
        'fecha_ingreso'     => '2026-06-11',
        'fecha_vencimiento' => '2028-12-31',
        'cantidad_inicial'  => 100,
        'cantidad_actual'   => 100,
        'costo_unitario'    => 1.50,
        'estado'            => 'Disponible'
    ]);

    $response = $this->actingAs($user)
                     ->get(route('central.lotes.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Lotes/Index')
        ->has('lotes.data')
    );
});

test('un usuario puede registrar un nuevo lote con datos validos', function () {
    $user = User::factory()->create();
    
    try {
        $producto = ProductoMaestro::factory()->create();
    } catch (\Throwable $e) {
        [$categoriaId, $laboratorioId, $proveedorId] = crearDependenciasProducto();
        
        $producto = ProductoMaestro::create([
            'categoria_id'   => $categoriaId,
            'laboratorio_id' => $laboratorioId,
            'proveedor_id'   => $proveedorId,
            'nombre_comercial' => 'Pfizer Vaccine',
            'sku'             => 'PFIZER-QA',
            'activo'          => true
        ]);
    }
    
    // Payload estructurado para LoteController::store
    $datosFormulario = [
        'producto_id'       => $producto->id,
        'numero_lote'       => 'LOT-PFIZER-01',
        'fecha_fabricacion' => '2026-01-15',
        'fecha_ingreso'     => '2026-06-11',
        'fecha_vencimiento' => '2027-06-15', // after:today
        'cantidad_inicial'  => 500,
        'cantidad_actual'   => 500,
        'costo_unitario'    => 1.50,
        'estado'            => 'Disponible'
    ];

    $response = $this->actingAs($user)
                     ->post(route('central.lotes.store'), $datosFormulario);

    $response->assertRedirect(route('central.lotes.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('central_lotes', [
        'numero_lote' => 'LOT-PFIZER-01',
        'producto_id' => $producto->id
    ]);
});

test('el sistema rechaza el registro de un lote si falta la fecha de vencimiento', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
                     ->from(route('central.lotes.create'))
                     ->post(route('central.lotes.store'), [
                         'producto_id'       => 999,
                         'numero_lote'       => 'LOT-ERROR',
                         'fecha_ingreso'     => '2026-06-11',
                         'cantidad_inicial'  => 50,
                         'cantidad_actual'   => 50,
                         'costo_unitario'    => 1.00,
                         'estado'            => 'Pendiente',
                         'fecha_vencimiento' => '' 
                     ]);

    $response->assertRedirect(route('central.lotes.create'));
    $response->assertSessionHasErrors(['fecha_vencimiento']);
});