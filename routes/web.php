<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TransaccionController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de CRUD Propiedades
    Route::get('/propiedades', [PropiedadController::class, 'index'])->name('propiedades.index');
    Route::get('/propiedades/create', [PropiedadController::class, 'create'])->name('propiedades.create');
    Route::post('/propiedades', [PropiedadController::class, 'store'])->name('propiedades.store');
    Route::get('/propiedades/{propiedad}/edit', [PropiedadController::class, 'edit'])->name('propiedades.edit');
    Route::patch('/propiedades/{propiedad}', [PropiedadController::class, 'update'])->name('propiedades.update');
    Route::delete('/propiedades/{propiedad}', [PropiedadController::class, 'destroy'])->name('propiedades.destroy');

 // Rutas de CRUD Clientes defino todas primero de acuerdoo a base previa 
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::patch('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');


// Rutas dCRUD Transacciones
Route::get('/transacciones', [TransaccionController::class, 'index'])->name('transacciones.index');
Route::get('/transacciones/create', [TransaccionController::class, 'create'])->name('transacciones.create');
Route::post('/transacciones', [TransaccionController::class, 'store'])->name('transacciones.store');
Route::get('/transacciones/{transaccion}/edit', [TransaccionController::class, 'edit'])->name('transacciones.edit');
Route::put('/transacciones/{transaccion}', [TransaccionController::class, 'update'])->name('transacciones.update');
Route::delete('/transacciones/{transaccion}', [TransaccionController::class, 'destroy'])->name('transacciones.destroy');

});

require __DIR__.'/auth.php';

