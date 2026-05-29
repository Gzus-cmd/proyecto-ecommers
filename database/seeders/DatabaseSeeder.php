<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sede;
use App\Models\Proveedor;
use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\ProductoMaestro;
use App\Models\Lote;
use App\Models\TipoMovimiento;
use App\Models\EstadoTransferencia;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Usuario
        User::create([
            'name' => 'Admin Central',
            'email' => 'admin@central.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Catálogos Estáticos
        $tiposMovimiento = [
            ['nombre' => 'Ingreso por Compra'],
            ['nombre' => 'Salida por Transferencia'],
            ['nombre' => 'Ajuste Positivo'],
            ['nombre' => 'Ajuste Negativo'],
            ['nombre' => 'Devolución'],
        ];
        foreach ($tiposMovimiento as $tipo) {
            TipoMovimiento::create($tipo);
        }

        $estadosTransferencia = [
            ['nombre' => 'Pendiente'],
            ['nombre' => 'En Tránsito'],
            ['nombre' => 'Recibido'],
            ['nombre' => 'Cancelado'],
        ];
        foreach ($estadosTransferencia as $estado) {
            EstadoTransferencia::create($estado);
        }

        // 3. Factories Independientes
        Sede::factory(5)->create();
        $proveedores = Proveedor::factory(10)->create();
        
        $categoriasNombres = ['Analgésicos', 'Antibióticos', 'Antiinflamatorios', 'Vitaminas', 'Dermatológicos'];
        $categorias = collect();
        foreach ($categoriasNombres as $cat) {
            $categorias->push(Categoria::factory()->create(['nombre' => $cat]));
        }

        $laboratorios = Laboratorio::factory(8)->create();

        // 4. Productos Maestros
        $productos = collect();
        for ($i = 0; $i < 50; $i++) {
            $productos->push(ProductoMaestro::factory()->create([
                'categoria_id' => $categorias->random()->id,
                'laboratorio_id' => $laboratorios->random()->id,
                'proveedor_id' => $proveedores->random()->id,
            ]));
        }

        // 5. Lotes
        foreach ($productos as $producto) {
            Lote::factory(rand(1, 3))->create([
                'producto_id' => $producto->id,
            ]);
        }
    }
}