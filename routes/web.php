<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;


use App\Http\Controllers\Central\ProductoMaestroController;
use App\Http\Controllers\Central\CategoriaController;
use App\Http\Controllers\Central\LaboratorioController;
use App\Http\Controllers\Central\ProveedorController;
use App\Http\Controllers\Central\LoteController;
use App\Http\Controllers\Central\SedeController;
use App\Http\Controllers\Central\TransferenciaController;
use App\Http\Controllers\Central\MovimientoInventarioController;
use App\Http\Controllers\Central\DashboardController;
use App\Http\Controllers\Central\UserController; 


Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::middleware(['auth', 'verified'])->prefix('central')->name('central.')->group(function () {
    

    Route::middleware(['can:usuarios.manage'])->group(function () {
        Route::resource('usuarios', UserController::class);
        Route::resource('sedes', SedeController::class);
    });


    Route::resource('productos', ProductoMaestroController::class)
        ->parameters(['productos' => 'productoMaestro']);
    
    Route::resource('categorias', CategoriaController::class);
    Route::resource('laboratorios', LaboratorioController::class);
    
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);


    Route::resource('lotes', LoteController::class);
    Route::resource('transferencias', TransferenciaController::class);


    Route::post('transferencias/{transferencia}/enviar', [TransferenciaController::class, 'enviar'])
        ->name('transferencias.enviar');
    
    Route::get('movimientos', [MovimientoInventarioController::class, 'index'])
        ->name('movimientos.index');
});

require __DIR__.'/settings.php';