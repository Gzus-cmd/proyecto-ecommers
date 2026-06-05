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



Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::middleware(['auth', 'verified'])->prefix('central')->name('central.')->group(function () {
    Route::resource('productos', ProductoMaestroController::class)
        ->parameters(['productos' => 'productoMaestro']);
    
    Route::resource('categorias', CategoriaController::class);

    Route::resource('laboratorios', LaboratorioController::class);

    Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor']);

    Route::resource('lotes', LoteController::class);

    Route::resource('sedes', SedeController::class);

    Route::resource('transferencias', TransferenciaController::class);

    Route::post('transferencias/{transferencia}/enviar', [TransferenciaController::class, 'enviar'])
        ->name('central.transferencias.enviar');
    
    Route::get('movimientos', [MovimientoInventarioController::class, 'index'])
    ->name('movimientos.index');
    
    });

require __DIR__.'/settings.php';
