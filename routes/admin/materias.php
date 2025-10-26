<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriaController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    // CRUD de materias anidado a carrera, con enforcement de ámbito
    Route::middleware(['ambito:carrera'])->group(function () {
        Route::get('carreras/{carrera}/materias', [MateriaController::class, 'index'])
            ->middleware('permission:ver_materias|gestionar_asignaturas')
            ->name('carreras.materias.index');

        Route::get('carreras/{carrera}/materias/create', [MateriaController::class, 'create'])
            ->middleware('permission:crear_materias|gestionar_asignaturas')
            ->name('carreras.materias.create');

        Route::post('carreras/{carrera}/materias', [MateriaController::class, 'store'])
            ->middleware('permission:crear_materias|gestionar_asignaturas')
            ->name('carreras.materias.store');

        Route::get('carreras/{carrera}/materias/{materia}/edit', [MateriaController::class, 'edit'])
            ->middleware('permission:editar_materias|gestionar_asignaturas')
            ->name('carreras.materias.edit');

        Route::put('carreras/{carrera}/materias/{materia}', [MateriaController::class, 'update'])
            ->middleware('permission:editar_materias|gestionar_asignaturas')
            ->name('carreras.materias.update');

        Route::delete('carreras/{carrera}/materias/{materia}', [MateriaController::class, 'destroy'])
            ->middleware('permission:eliminar_materias|gestionar_asignaturas')
            ->name('carreras.materias.destroy');

        // Restaurar (opcional)
        Route::post('carreras/{carrera}/materias/{id_materia}/restore', [MateriaController::class, 'restore'])
            ->middleware('permission:restaurar_materias|gestionar_asignaturas')
            ->name('carreras.materias.restore');
    });
});
