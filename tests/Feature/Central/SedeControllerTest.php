<?php

namespace Tests\Feature\Central;

use App\Models\User;
use App\Models\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('un usuario autenticado puede listar las sedes y usar el buscador por codigo o nombre', function () {
    $user = User::factory()->create();

    // Creamos sedes de prueba para validar el comportamiento del filtro
    Sede::create([
        'codigo' => 'SED-LIM-01',
        'nombre' => 'Sede Principal Lima',
        'direccion' => 'Av. Javier Prado 456',
        'telefono' => '014445555',
        'activo' => true
    ]);

    Sede::create([
        'codigo' => 'SED-ARE-02',
        'nombre' => 'Almacen Arequipa',
        'direccion' => 'Calle Mercaderes 123',
        'telefono' => '054223344',
        'activo' => true
    ]);

    // Ejecutamos la búsqueda filtrando por el código único
    $response = $this->actingAs($user)
                     ->get(route('central.sedes.index', ['search' => 'SED-ARE-02']));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Sedes/Index')
        ->has('sedes.data')
        ->where('filters.search', 'SED-ARE-02')
    );
});

test('un usuario puede acceder al formulario de creacion de sedes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('central.sedes.create'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Sedes/Create')
    );
});

test('un usuario puede almacenar una nueva sede con todos sus campos obligatorios', function () {
    $user = User::factory()->create();

    $payload = [
        'codigo' => 'SED-TRU-03',
        'nombre' => 'Sucursal Trujillo Centro',
        'direccion' => 'Jr. Pizarro 789',
        'telefono' => '044998877',
        'activo' => true // Obligatorio según la regla del controlador
    ];

    $response = $this->actingAs($user)->post(route('central.sedes.store'), $payload);

    $response->assertRedirect(route('central.sedes.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('central_sedes', [
        'codigo' => 'SED-TRU-03',
        'nombre' => 'Sucursal Trujillo Centro'
    ]);
});

test('un usuario puede actualizar los datos de una sede existente respetando la excepcion del codigo unico', function () {
    $user = User::factory()->create();

    $sede = Sede::create([
        'codigo' => 'SED-CHY-04',
        'nombre' => 'Sede Chiclayo Antigua',
        'activo' => true
    ]);

    $payload = [
        'codigo' => 'SED-CHY-04', // Mismo código para probar la regla de exclusión del Unique
        'nombre' => 'Sede Chiclayo Renovada',
        'direccion' => 'Av. Balta 555',
        'activo' => false
    ];

    $response = $this->actingAs($user)->put(route('central.sedes.update', $sede->id), $payload);

    $response->assertRedirect(route('central.sedes.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('central_sedes', [
        'id' => $sede->id,
        'nombre' => 'Sede Chiclayo Renovada',
        'activo' => 0
    ]);
});

test('un usuario puede eliminar permanentemente una sede del sistema', function () {
    $user = User::factory()->create();

    $sede = Sede::create([
        'codigo' => 'SED-DEL-99',
        'nombre' => 'Sede Temporal a Borrar',
        'activo' => true
    ]);

    $response = $this->actingAs($user)->delete(route('central.sedes.destroy', $sede->id));

    $response->assertRedirect(route('central.sedes.index'));
    $response->assertSessionHas('success', 'Sede eliminada permanentemente.');
    $this->assertDatabaseMissing('central_sedes', ['id' => $sede->id]);
});