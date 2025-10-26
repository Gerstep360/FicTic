<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrupoController;

Route::prefix('admin')->middleware(['auth'])->group(function () {

    // ====== NUEVO: selector de materias para grupos ======
    Route::middleware(['ambito:carrera'])->group(function () {
        // Ruta “grupos.materias” (como pediste)
        Route::get('carreras/{carrera}/grupos/materias', [GrupoController::class, 'materias'])
            ->middleware('permission:ver_grupos|gestionar_grupos')
            ->name('grupos.materias');

        // Alias más corto y fácil de recordar
        Route::get('carreras/{carrera}/grupos', [GrupoController::class, 'materias'])
            ->middleware('permission:ver_grupos|gestionar_grupos')
            ->name('carreras.grupos');
    });

    // ====== CRUD de grupos anidado bajo materia ======
    Route::middleware(['ambito:carrera'])->group(function () {
        Route::get('carreras/{carrera}/materias/{materia}/grupos', [GrupoController::class, 'index'])
            ->middleware('permission:ver_grupos|gestionar_grupos')
            ->name('carreras.materias.grupos.index');

        Route::get('carreras/{carrera}/materias/{materia}/grupos/create', [GrupoController::class, 'create'])
            ->middleware('permission:crear_grupos|gestionar_grupos')
            ->name('carreras.materias.grupos.create');

        Route::post('carreras/{carrera}/materias/{materia}/grupos', [GrupoController::class, 'store'])
            ->middleware('permission:crear_grupos|gestionar_grupos')
            ->name('carreras.materias.grupos.store');

        Route::get('carreras/{carrera}/materias/{materia}/grupos/{grupo}/edit', [GrupoController::class, 'edit'])
            ->middleware('permission:editar_grupos|gestionar_grupos')
            ->name('carreras.materias.grupos.edit');

        Route::put('carreras/{carrera}/materias/{materia}/grupos/{grupo}', [GrupoController::class, 'update'])
            ->middleware('permission:editar_grupos|gestionar_grupos')
            ->name('carreras.materias.grupos.update');

        Route::delete('carreras/{carrera}/materias/{materia}/grupos/{grupo}', [GrupoController::class, 'destroy'])
            ->middleware('permission:eliminar_grupos|gestionar_grupos')
            ->name('carreras.materias.grupos.destroy');
    });
});