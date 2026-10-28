<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
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

    Route::resource('propiedades', PropiedadController::class)
        ->parameters(['propiedades' => 'propiedad']);

    Route::resource('clientes', ClienteController::class)
        ->except('show');

    Route::resource('transacciones', TransaccionController::class)
        ->parameters(['transacciones' => 'transaccion'])
        ->except('show');
});

require __DIR__.'/auth.php';
