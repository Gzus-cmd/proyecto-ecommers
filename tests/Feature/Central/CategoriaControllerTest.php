<?php

namespace Tests\Feature\Central;

use App\Models\Categoria;
use App\Models\User;
use App\Models\ProductoMaestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('un usuario autenticado puede listar categorias y buscar por nombre', function () {
    $user = User::factory()->create();
    Categoria::create(['nombre' => 'Antibióticos', 'descripcion' => 'Controlados']);
    Categoria::create(['nombre' => 'Analgésicos', 'descripcion' => 'Venta libre']);

    $response = $this->actingAs($user)->get(route('central.categorias.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Central/Categorias/Index')
        ->has('categorias.data')
    );

    // Probamos el filtro de búsqueda específico de farmacia
    $responseSearch = $this->actingAs($user)
                           ->get(route('central.categorias.index', ['search' => 'Antibio']));
    
    $responseSearch->assertStatus(200);
});

test('un usuario puede registrar una nueva categoria con datos validos', function () {
    $user = User::factory()->create();
    
    $datosFormulario = [
        'nombre' => 'Vitaminas',
        'descripcion' => 'Suplementos alimenticios'
    ];

    $response = $this->actingAs($user)->post(route('central.categorias.store'), $datosFormulario);

    $response->assertRedirect(route('central.categorias.index'));
    $response->assertSessionHas('success', 'Categoría creada correctamente.');
    $this->assertDatabaseHas('central_categorias', ['nombre' => 'Vitaminas']);
});

test('el sistema rechaza la creacion de una categoria si el nombre ya existe', function () {
    $user = User::factory()->create();
    Categoria::create(['nombre' => 'Jarabes']);

    $response = $this->actingAs($user)
                     ->from(route('central.categorias.create'))
                     ->post(route('central.categorias.store'), [
                         'nombre' => 'Jarabes',
                         'descripcion' => 'Duplicado'
                     ]);

    $response->assertRedirect(route('central.categorias.create'));
    $response->assertSessionHasErrors(['nombre']);
});

test('un usuario puede actualizar una categoria respetando la regla del nombre unico', function () {
    $user = User::factory()->create();
    $categoria = Categoria::create(['nombre' => 'Dermatológicos']);

    $payload = [
        'nombre' => 'Dermatología Avanzada',
        'descripcion' => 'Cuidado de la piel'
    ];

    $response = $this->actingAs($user)->put(route('central.categorias.update', $categoria->id), $payload);

    $response->assertRedirect(route('central.categorias.index'));
    $response->assertSessionHas('success', 'Categoría actualizada correctamente.');
    $this->assertDatabaseHas('central_categorias', [
        'id' => $categoria->id,
        'nombre' => 'Dermatología Avanzada'
    ]);
});

test('el sistema impide eliminar una categoria si tiene productos farmaceuticos asociados', function () {
    $user = User::factory()->create();
    $categoria = Categoria::create(['nombre' => 'Inyectables']);

    // Mockeamos dependencias mínimas requeridas por ProductoMaestro para evitar errores de BD
    $laboratorioId = DB::table('central_laboratorios')->insertGetId([
        'nombre' => 'Lab Test', 'created_at' => now(), 'updated_at' => now()
    ]);
    
    $colProveedor = Schema::hasColumn('central_proveedores', 'nombre') ? 'nombre' : 'razon_social';
    $proveedorId = DB::table('central_proveedores')->insertGetId([
        $colProveedor => 'Proveedor Test',
        'ruc' => '20111222333',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Asociamos un producto real a la categoría
    ProductoMaestro::create([
        'sku' => 'SKU-INY-01',
        'nombre_comercial' => 'Amikacina 500mg',
        'categoria_id' => $categoria->id,
        'laboratorio_id' => $laboratorioId,
        'proveedor_id' => $proveedorId,
    ]);

    // Intentamos eliminar la categoría que tiene el producto amarrado
    $response = $this->actingAs($user)->delete(route('central.categorias.destroy', $categoria->id));

    $response->assertSessionHas('error', 'No se puede eliminar: la categoría tiene productos asociados.');
    $this->assertDatabaseHas('central_categorias', ['id' => $categoria->id]);
});