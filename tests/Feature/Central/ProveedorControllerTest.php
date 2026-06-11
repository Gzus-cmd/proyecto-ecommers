<?php

namespace Tests\Feature\Central;

use App\Models\User;
use App\Models\Proveedor;
use App\Models\ProductoMaestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('un usuario autenticado puede listar los proveedores y usar el buscador', function () {
    $user = User::factory()->create();

    // Insertamos dos proveedores para validar los filtros
    Proveedor::create([
        'razon_social' => 'Distribuidora Medica Alfa',
        'ruc' => '20111222333',
        'contacto' => 'Carlos Perez',
        'activo' => true
    ]);

    Proveedor::create([
        'razon_social' => 'Drogueria Beta',
        'ruc' => '20444555666',
        'contacto' => 'Ana Gomez',
        'activo' => false
    ]);

    // Probamos el buscador filtrando por RUC
    $response = $this->actingAs($user)
                     ->get(route('central.proveedores.index', ['search' => '20111222333']));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Proveedores/Index')
        ->has('proveedores.data')
        ->where('filters.search', '20111222333')
    );
});

test('un usuario puede almacenar un nuevo proveedor valido', function () {
    $user = User::factory()->create();

    $payload = [
        'razon_social' => 'Proveedor Express S.A.C.',
        'ruc' => '20999888777',
        'contacto' => 'Juan QA',
        'telefono' => '999888777',
        'email' => 'juan@qa.com',
        'direccion' => 'Av. Central 123',
        'activo' => true
    ];

    $response = $this->actingAs($user)->post(route('central.proveedores.store'), $payload);

    $response->assertRedirect(route('central.proveedores.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('central_proveedores', [
        'ruc' => '20999888777',
        'razon_social' => 'Proveedor Express S.A.C.'
    ]);
});

test('un usuario puede ver el detalle formateado de un proveedor', function () {
    $user = User::factory()->create();

    $proveedor = Proveedor::create([
        'razon_social' => 'Farma Corp',
        'ruc' => '20555666777',
        'activo' => true
    ]);

    $response = $this->actingAs($user)->get(route('central.proveedores.show', $proveedor->id));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Proveedores/Show')
        ->where('proveedor.razon_social', 'Farma Corp')
        ->where('proveedor.ruc', '20555666777')
    );
});

test('el sistema rechaza la eliminacion de un proveedor si este tiene productos asociados', function () {
    $user = User::factory()->create();

    // 1. Creamos el proveedor
    $proveedor = Proveedor::create([
        'razon_social' => 'Proveedor Con Productos',
        'ruc' => '20888999111',
        'activo' => true
    ]);

    // 2. Generamos las dependencias mínimas para ProductoMaestro
    $categoriaId = DB::table('central_categorias')->insertGetId([
        'nombre' => 'General', 'created_at' => now(), 'updated_at' => now()
    ]);
    $laboratorioId = DB::table('central_laboratorios')->insertGetId([
        'nombre' => 'Lab General', 'created_at' => now(), 'updated_at' => now()
    ]);

    // 3. Vinculamos un producto para disparar la restricción del destroy
    ProductoMaestro::create([
        'sku' => 'SKU-PROV-LOCK',
        'nombre_comercial' => 'Producto de Prueba QA',
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedor->id,
    ]);

    // 4. Intentamos eliminar
    $response = $this->actingAs($user)->delete(route('central.proveedores.destroy', $proveedor->id));

    $response->assertSessionHas('error', 'No se puede eliminar: el proveedor tiene productos asociados.');
    $this->assertDatabaseHas('central_proveedores', ['id' => $proveedor->id]);
});