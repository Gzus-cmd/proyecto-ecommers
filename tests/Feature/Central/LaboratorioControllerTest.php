<?php

namespace Tests\Feature\Central;

use App\Models\Laboratorio;
use App\Models\User;
use App\Models\ProductoMaestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('un usuario autenticado puede listar laboratorios y buscar por nombre o pais', function () {
    $user = User::factory()->create();
    
    // Usamos 'pais' en lugar de 'descripcion' según la migración real
    Laboratorio::create(['nombre' => 'Pfizer', 'pais' => 'Estados Unidos']);
    Laboratorio::create(['nombre' => 'Bayer', 'pais' => 'Alemania']);

    $response = $this->actingAs($user)->get(route('central.laboratorios.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Laboratorios/Index')
        ->has('laboratorios.data')
    );

    // Probamos el buscador filtrando por el país del laboratorio
    $responseSearch = $this->actingAs($user)
                           ->get(route('central.laboratorios.index', ['search' => 'Alemania']));
    
    $responseSearch->assertStatus(200);
});

test('un usuario puede registrar un nuevo laboratorio farmaceutico con datos validos', function () {
    $user = User::factory()->create();
    
    $datosFormulario = [
        'nombre' => 'Roche',
        'pais' => 'Suiza'
    ];

    $response = $this->actingAs($user)->post(route('central.laboratorios.store'), $datosFormulario);

    $response->assertRedirect(route('central.laboratorios.index'));
    $response->assertSessionHas('success', 'Laboratorio creado correctamente.');

    $this->assertDatabaseHas('central_laboratorios', [
        'nombre' => 'Roche',
        'pais' => 'Suiza'
    ]);
});

test('el sistema rechaza la creacion de un laboratorio si el nombre ya existe', function () {
    $user = User::factory()->create();
    Laboratorio::create(['nombre' => 'Farmaindustria', 'pais' => 'Perú']);

    $response = $this->actingAs($user)
                     ->from(route('central.laboratorios.create'))
                     ->post(route('central.laboratorios.store'), [
                         'nombre' => 'Farmaindustria',
                         'pais' => 'Internacional'
                     ]);

    $response->assertRedirect(route('central.laboratorios.create'));
    $response->assertSessionHasErrors(['nombre']);
});

test('un usuario puede actualizar los datos de un laboratorio existente', function () {
    $user = User::factory()->create();
    $laboratorio = Laboratorio::create(['nombre' => 'Sanofi', 'pais' => 'Francia']);

    $payload = [
        'nombre' => 'Sanofi Aventis',
        'pais' => 'Francia Europa'
    ];

    $response = $this->actingAs($user)->put(route('central.laboratorios.update', $laboratorio->id), $payload);

    $response->assertRedirect(route('central.laboratorios.index'));
    $response->assertSessionHas('success', 'Laboratorio actualizado correctamente.');
    $this->assertDatabaseHas('central_laboratorios', [
        'id' => $laboratorio->id,
        'nombre' => 'Sanofi Aventis',
        'pais' => 'Francia Europa'
    ]);
});

test('el sistema impide eliminar un laboratorio si tiene productos asociados', function () {
    $user = User::factory()->create();
    $laboratorio = Laboratorio::create(['nombre' => 'Bago', 'pais' => 'Argentina']);

    // Mockeamos dependencias mínimas requeridas de la base de datos
    $categoriaId = DB::table('central_categorias')->insertGetId([
        'nombre' => 'Analgesicos', 'created_at' => now(), 'updated_at' => now()
    ]);
    
    $colProveedor = Schema::hasColumn('central_proveedores', 'nombre') ? 'nombre' : 'razon_social';
    $proveedorId = DB::table('central_proveedores')->insertGetId([
        $colProveedor => 'Proveedor Global',
        'ruc' => '20999888777',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Asociamos un producto real al laboratorio
    ProductoMaestro::create([
        'sku' => 'SKU-BAG-05',
        'nombre_comercial' => 'Aspirina 100mg',
        'categoria_id' => $categoriaId,
        'laboratorio_id' => $laboratorio->id,
        'proveedor_id' => $proveedorId,
    ]);

    // Intentamos eliminar el laboratorio comprometido con stock/catálogo
    $response = $this->actingAs($user)->delete(route('central.laboratorios.destroy', $laboratorio->id));

    $response->assertSessionHas('error', 'No se puede eliminar: el laboratorio tiene productos asociados.');
    $this->assertDatabaseHas('central_laboratorios', ['id' => $laboratorio->id]);
});