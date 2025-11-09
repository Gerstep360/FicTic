<?php

use App\Http\Controllers\HorarioClaseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('horarios.')->group(function () {
    // Ruta para validación AJAX
    Route::post('horarios/validar', [HorarioClaseController::class, 'validarConflictosAjax'])
        ->name('validar');

    // CRUD de horarios
    Route::resource('horarios', HorarioClaseController::class)
        ->parameters(['horarios' => 'horario'])
        ->names([
            'index'   => 'index',
            'create'  => 'create',
            'store'   => 'store',
            'show'    => 'show',
            'edit'    => 'edit',
            'update'  => 'update',
            'destroy' => 'destroy',
        ]);
});
