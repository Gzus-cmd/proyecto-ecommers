<?php

namespace Tests\Feature\Central;

use App\Models\User;
use App\Models\Lote;
use App\Models\ProductoMaestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);


function crearEntornoInventarioEstructural(): array {
    // 1. Dependencias obligatorias de ProductoMaestro
    $categoriaId = DB::table('central_categorias')->insertGetId([
        'nombre' => 'Categoria QA', 'created_at' => now(), 'updated_at' => now()
    ]);
    $laboratorioId = DB::table('central_laboratorios')->insertGetId([
        'nombre' => 'Lab QA', 'created_at' => now(), 'updated_at' => now()
    ]);
    
    $colProveedor = Schema::hasColumn('central_proveedores', 'nombre') ? 'nombre' : 'razon_social';
    $datosProv = [$colProveedor => 'Proveedor QA', 'created_at' => now(), 'updated_at' => now()];
    if (Schema::hasColumn('central_proveedores', 'ruc')) {
        $datosProv['ruc'] = '20111222333';
    }
    $proveedorId = DB::table('central_proveedores')->insertGetId($datosProv);

    // 2. Crear Producto Maestro
    $producto = ProductoMaestro::create([
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedorId,
        'nombre_comercial' => 'Amoxicilina QA',
        'sku' => 'AMX-99',
        'activo' => true
    ]);

    // 3. Crear Lote funcional
    $lote = Lote::create([
        'producto_id' => $producto->id,
        'numero_lote' => 'LOT-QA-NIGHTLY',
        'fecha_fabricacion' => '2026-01-01',
        'fecha_ingreso' => '2026-06-11',
        'fecha_vencimiento' => '2027-12-31',
        'cantidad_inicial' => 100,
        'cantidad_actual' => 100,
        'costo_unitario' => 3.50,
        'estado' => 'Disponible'
    ]);

    // 4. Crear el registro en la tabla de tipos de movimiento
    $tipoMovimientoId = DB::table('central_tipos_movimiento')->insertGetId([
        'nombre' => 'Entrada por Compra',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 5. Crear el usuario que registra la acción en el sistema
    $usuarioAuditoria = User::factory()->create();

    return [$lote, $tipoMovimientoId, $usuarioAuditoria];
}

test('un usuario autenticado puede renderizar el listado de movimientos de inventario con sus relaciones', function () {
    $user = User::factory()->create();
    [$lote, $tipoMovimientoId, $usuarioAuditoria] = crearEntornoInventarioEstructural();

    // Insertamos respetando de forma matemática la estructura de la migración dada
    DB::table('central_movimientos_inventario')->insert([
        'lote_id'            => $lote->id,
        'tipo_movimiento_id' => $tipoMovimientoId,
        'movimentable_type'  => 'App\Models\Transferencia', // Simulación polimórfica obligatoria
        'movimentable_id'    => 1,
        'cantidad'           => 50,
        'stock_antes'        => 100,
        'stock_despues'      => 150,
        'usuario_id'         => $usuarioAuditoria->id,
        'fecha_movimiento'   => now(),
        'observacion'        => 'QA Test: Carga de Index exitosa',
        'created_at'         => now(),
        'updated_at'         => now(),
    ]);

    $response = $this->actingAs($user)
                     ->get(route('central.movimientos.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Movimientos/Index')
        ->has('movimientos.data')
    );
});

test('un usuario puede filtrar los movimientos de inventario mediante el buscador', function () {
    $user = User::factory()->create();
    [$lote, $tipoMovimientoId, $usuarioAuditoria] = crearEntornoInventarioEstructural();

    DB::table('central_movimientos_inventario')->insert([
        'lote_id'            => $lote->id,
        'tipo_movimiento_id' => $tipoMovimientoId,
        'movimentable_type'  => 'App\Models\Transferencia',
        'movimentable_id'    => 1,
        'cantidad'           => -10,
        'stock_antes'        => 150,
        'stock_despues'      => 140,
        'usuario_id'         => $usuarioAuditoria->id,
        'fecha_movimiento'   => now(),
        'observacion'        => 'QA Test: Filtro por número de lote',
        'created_at'         => now(),
        'updated_at'         => now(),
    ]);

    // Ejecutamos la búsqueda mandando el número de lote exacto creado en el entorno
    $response = $this->actingAs($user)
                     ->get(route('central.movimientos.index', ['search' => 'LOT-QA-NIGHTLY']));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Movimientos/Index')
        ->where('filters.search', 'LOT-QA-NIGHTLY')
    );
});