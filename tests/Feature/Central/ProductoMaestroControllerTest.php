<?php

namespace Tests\Feature\Central;

use App\Models\User;
use App\Models\ProductoMaestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

/**
 *para generar las dependencias exactas requeridas por las llaves foráneas.
 */
function crearDependenciasMaestras(): array {
    $categoriaId = DB::table('central_categorias')->insertGetId([
        'nombre' => 'Antibióticos', 'created_at' => now(), 'updated_at' => now()
    ]);

    $laboratorioId = DB::table('central_laboratorios')->insertGetId([
        'nombre' => 'Pfizer QA', 'created_at' => now(), 'updated_at' => now()
    ]);

    $colProveedor = Schema::hasColumn('central_proveedores', 'nombre') ? 'nombre' : 'razon_social';
    $datosProv = [
        $colProveedor => 'Distribuidora Farma QA',
        'created_at'  => now(),
        'updated_at'  => now()
    ];

    if (Schema::hasColumn('central_proveedores', 'ruc')) {
        $datosProv['ruc'] = '20777888999';
    }
    if (Schema::hasColumn('central_proveedores', 'activo')) {
        $datosProv['activo'] = true;
    }

    $proveedorId = DB::table('central_proveedores')->insertGetId($datosProv);

    return [$categoriaId, $laboratorioId, $proveedorId];
}

test('un usuario autenticado puede listar los productos maestros y filtrar por busqueda', function () {
    $user = User::factory()->create();
    [$categoriaId, $laboratorioId, $proveedorId] = crearDependenciasMaestras();

    ProductoMaestro::create([
        'sku' => 'SKU-FOR-SEARCH-01',
        'nombre_comercial' => 'Paracetamol Senior',
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedorId,
    ]);

    $response = $this->actingAs($user)
                     ->get(route('central.productos.index', ['search' => 'Paracetamol']));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Productos/Index')
        ->has('productos.data')
        ->where('filters.search', 'Paracetamol')
    );
});

test('un usuario puede acceder a la pantalla de creacion con catalogos ordenados', function () {
    $user = User::factory()->create();
    crearDependenciasMaestras();

    $response = $this->actingAs($user)->get(route('central.productos.create'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Productos/Create')
        ->has('categorias')
        ->has('laboratorios')
        ->has('proveedores')
    );
});

test('un usuario puede almacenar un nuevo producto maestro valido', function () {
    $user = User::factory()->create();
    [$categoriaId, $laboratorioId, $proveedorId] = crearDependenciasMaestras();

    $payload = [
        'sku' => 'SKU-NUEVO-100',
        'nombre_comercial' => 'Ibuprofeno 400mg',
        'nombre_generico' => 'Ibuprofeno',
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedorId,
        'requiere_receta' => false,
        'stock_minimo' => 10,
        'activo' => true
    ];

    $response = $this->actingAs($user)->post(route('central.productos.store'), $payload);

    $response->assertRedirect(route('central.productos.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('central_productos_maestro', ['sku' => 'SKU-NUEVO-100']);
});

test('el sistema rechaza la eliminacion de un producto maestro si este cuenta con lotes vinculados', function () {
    $user = User::factory()->create();
    [$categoriaId, $laboratorioId, $proveedorId] = crearDependenciasMaestras();

    $producto = ProductoMaestro::create([
        'sku' => 'SKU-CON-LOTES',
        'nombre_comercial' => 'Producto Bloqueado',
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedorId,
    ]);

    // Insertamos manualmente un lote vinculado para detonar la restriccion del controlador
    DB::table('central_lotes')->insert([
        'producto_id' => $producto->id,
        'numero_lote' => 'LOT-BLOCK-01',
        'fecha_fabricacion' => '2026-01-01',
        'fecha_ingreso' => '2026-06-11',
        'fecha_vencimiento' => '2027-12-31',
        'cantidad_inicial' => 50,
        'cantidad_actual' => 50,
        'costo_unitario' => 2.00,
        'estado' => 'Disponible',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    $response = $this->actingAs($user)->delete(route('central.productos.destroy', $producto->id));

    $response->assertSessionHas('error', 'No se puede eliminar: el producto tiene lotes registrados.');
    $this->assertDatabaseHas('central_productos_maestro', ['id' => $producto->id]);
});