<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserAmbitoController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\FacultadController;

/*
|--------------------------------------------------------------------------
| Asignación de Ámbitos (UI/CRUD)
| - Administra qué rol+ámbito tiene cada usuario.
| - Protegido con permisos específicos de asignación.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // LISTAR usuarios para asignar ámbitos (sin {id} en la URL)
    Route::get('usuarios/ambitos', [UserAmbitoController::class, 'browse'])
        ->middleware('permission:ver_asignaciones_ambito')
        ->name('usuarios.ambitos.browse');

    // Pantalla de asignación para un usuario
    Route::get('usuarios/{user}/ambitos', [UserAmbitoController::class, 'index'])
        ->middleware('permission:ver_asignaciones_ambito')
        ->name('usuarios.ambitos.index');

    Route::post('usuarios/{user}/ambitos', [UserAmbitoController::class, 'store'])
        ->middleware('permission:asignar_perfiles_ambitos')
        ->name('usuarios.ambitos.store');

    Route::delete('usuarios/{user}/ambitos/{ambito}', [UserAmbitoController::class, 'destroy'])
        ->middleware('permission:eliminar_asignacion_ambito')
        ->name('usuarios.ambitos.destroy');
});

/*
|--------------------------------------------------------------------------
| Rutas de negocio con enforcement por Ámbito
| - Aplica el middleware 'ambito:*' para filtrar por carrera/facultad.
| - El permiso define "qué puede hacer", el ámbito define "sobre qué".
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {
        // Opción A (model binding): {carrera} -> App\Models\Carrera
        Route::get('carreras/{carrera}', [CarreraController::class, 'show'])
            ->middleware(['permission:gestionar_asignaturas', 'ambito:carrera'])
            ->name('carreras.show');

        // Opción B (id numérico): descomenta estas 3 líneas y borra la ruta anterior si usas {id_carrera}
        // Route::get('carreras/{id_carrera}', [CarreraController::class, 'show'])
        //     ->middleware(['permission:gestionar_asignaturas', 'ambito:carrera,id_carrera'])
        //     ->name('carreras.showById');

        // Facultades con enforcement por ámbito
        Route::get('facultades/{facultad}', [FacultadController::class, 'show'])
            ->middleware(['permission:ver_reportes', 'ambito:facultad'])
            ->name('facultades.show');

        // (Opcional) Aplicar ámbito a TODO el resource de Carreras:
        // Route::resource('carreras', CarreraController::class)
        //     ->middleware('ambito:carrera');
    });
