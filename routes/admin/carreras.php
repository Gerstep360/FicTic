<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarreraController;

Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Listado y detalle (solo requiere login)
    Route::get('carreras',              [CarreraController::class, 'index'])->name('carreras.index');
    Route::get('carreras/{carrera}',    [CarreraController::class, 'show'])->name('carreras.show');

    // Crear / Guardar (requiere CU-02: registrar_unidades_academicas)
    Route::get('carreras/create',       [CarreraController::class, 'create'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('carreras.create');

    Route::post('carreras',             [CarreraController::class, 'store'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('carreras.store');

    // Editar / Actualizar (requiere CU-02)
    Route::get('carreras/{carrera}/edit', [CarreraController::class, 'edit'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('carreras.edit');

    Route::put('carreras/{carrera}',    [CarreraController::class, 'update'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('carreras.update');
});
