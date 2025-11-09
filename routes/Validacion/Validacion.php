<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValidacionHorarioController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    
    // Panel de validación
    Route::get('/validacion-horarios', [ValidacionHorarioController::class, 'index'])
        ->name('validacion-horarios.index');
    
    // Ejecutar validación
    Route::post('/validacion-horarios/validar', [ValidacionHorarioController::class, 'validar'])
        ->name('validacion-horarios.validar');
    
    // Gestión de reglas
    Route::get('/validacion-horarios/reglas', [ValidacionHorarioController::class, 'reglas'])
        ->name('validacion-horarios.reglas');
    
    Route::post('/validacion-horarios/reglas', [ValidacionHorarioController::class, 'storeRegla'])
        ->name('validacion-horarios.reglas.store');
    
    Route::put('/validacion-horarios/reglas/{regla}', [ValidacionHorarioController::class, 'updateRegla'])
        ->name('validacion-horarios.reglas.update');
    
    Route::delete('/validacion-horarios/reglas/{regla}', [ValidacionHorarioController::class, 'destroyRegla'])
        ->name('validacion-horarios.reglas.destroy');
    
    Route::patch('/validacion-horarios/reglas/{regla}/toggle', [ValidacionHorarioController::class, 'toggleRegla'])
        ->name('validacion-horarios.reglas.toggle');
});
