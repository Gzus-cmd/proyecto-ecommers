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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. INFRAESTRUCTURA DE SEGURIDAD (Permisos Atómicos) ---
        $permissions = [
           
            'usuarios.manage',      
            'sedes.manage',         
            
            
            'maestros.create',      
            'maestros.update',      
            'maestros.delete',      
            
            
            'inventario.view',      
            'inventario.ajustar',   
            
            
            'transferencias.create', 
            'transferencias.enviar', 
        ];

        foreach ($permissions as $p) {
            Permission::create(['name' => $p]);
        }

        // --- 2. DEFINICIÓN DE ROLES ---

        // Administrador General: Acceso Total
        $roleAdmin = Role::create(['name' => 'Administrador General']);
        $roleAdmin->givePermissionTo(Permission::all());

        // Jefe de Almacén: Gestión operativa completa
        $roleJefe = Role::create(['name' => 'Jefe de Almacén']);
        $roleJefe->givePermissionTo([
            'maestros.create', 
            'maestros.update', 
            'inventario.view', 
            'inventario.ajustar', 
            'transferencias.create',
            'transferencias.enviar'
        ]);

        // Auxiliar de Almacén: Solo consulta y preparación
        $roleAuxiliar = Role::create(['name' => 'Auxiliar de Almacén']);
        $roleAuxiliar->givePermissionTo([
            'inventario.view', 
            'transferencias.create'
        ]);

        // --- 3. USUARIO MAESTRO ÚNICO (ADMIN CENTRAL) ---

        $userMaster = User::create([
            'name' => 'ADMINISTRADOR CENTRAL',
            'email' => 'admin@central.com',
            'password' => Hash::make('12345678'), 
        ]);
        $userMaster->assignRole($roleAdmin);

        // --- 4. CATÁLOGOS ESTÁTICOS (REGLAS DE NEGOCIO) ---

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

        // --- 5. GENERACIÓN DE DATA MAESTRA (FACTORIES) ---

        // Sedes y Proveedores
        Sede::factory(5)->create();
        $proveedores = Proveedor::factory(10)->create();
        
        // Categorías Técnicas
        $categoriasNombres = ['Analgésicos', 'Antibióticos', 'Antiinflamatorios', 'Vitaminas', 'Dermatológicos'];
        $categorias = collect();
        foreach ($categoriasNombres as $cat) {
            $categorias->push(Categoria::factory()->create(['nombre' => $cat]));
        }

        // Laboratorios
        $laboratorios = Laboratorio::factory(8)->create();

        // --- 6. PRODUCTOS Y CONTROL DE LOTES ---

        $productos = collect();
        for ($i = 0; $i < 50; $i++) {
            $productos->push(ProductoMaestro::factory()->create([
                'categoria_id' => $categorias->random()->id,
                'laboratorio_id' => $laboratorios->random()->id,
                'proveedor_id' => $proveedores->random()->id,
            ]));
        }

        // Generación de Lotes para alimentar el Stock inicial
        foreach ($productos as $producto) {
            Lote::factory(rand(1, 3))->create([
                'producto_id' => $producto->id,
            ]);
        }
    }
}