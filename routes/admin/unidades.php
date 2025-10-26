<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacultadController;

Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Listado y detalle (sólo autenticado)
    Route::get('facultades',                 [FacultadController::class, 'index'])->name('facultades.index');
    Route::get('facultades/{facultad}',      [FacultadController::class, 'show'])->name('facultades.show');

    // Crear / Guardar (requiere permiso CU-02)
    Route::get('facultades/create',          [FacultadController::class, 'create'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('facultades.create');

    Route::post('facultades',                [FacultadController::class, 'store'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('facultades.store');

    // Editar / Actualizar (requiere permiso CU-02)
    Route::get('facultades/{facultad}/edit', [FacultadController::class, 'edit'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('facultades.edit');

    Route::put('facultades/{facultad}',      [FacultadController::class, 'update'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('facultades.update');

    // Alias para el menú "Unidades Académicas"
    Route::get('unidades', fn () => redirect()->route('facultades.index'))->name('unidades.index');
});
