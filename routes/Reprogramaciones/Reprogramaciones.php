<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReprogramacionController;

/*
|--------------------------------------------------------------------------
| CU-23: Reprogramaciones y Cambios de Aula
|--------------------------------------------------------------------------
| Gestiona ajustes al horario publicado debido a contingencias
*/

// Rutas para gestión de reprogramaciones
Route::middleware(['auth'])->group(function () {
    
    // Listado y visualización
    Route::get('/admin/reprogramaciones', [ReprogramacionController::class, 'index'])
        ->name('reprogramaciones.index');
    
    Route::get('/admin/reprogramaciones/{reprogramacion}', [ReprogramacionController::class, 'show'])
        ->name('reprogramaciones.show');
    
    // Creación (requiere permiso)
    Route::middleware(['permission:gestionar_reprogramaciones'])->group(function () {
        Route::get('/admin/reprogramaciones/crear', [ReprogramacionController::class, 'create'])
            ->name('reprogramaciones.create');
        
        Route::post('/admin/reprogramaciones', [ReprogramacionController::class, 'store'])
            ->name('reprogramaciones.store');
    });
    
    // Aprobación/Rechazo (requiere permiso especial)
    Route::middleware(['permission:aprobar_reprogramaciones'])->group(function () {
        Route::post('/admin/reprogramaciones/{reprogramacion}/aprobar', [ReprogramacionController::class, 'aprobar'])
            ->name('reprogramaciones.aprobar');
        
        Route::post('/admin/reprogramaciones/{reprogramacion}/rechazar', [ReprogramacionController::class, 'rechazar'])
            ->name('reprogramaciones.rechazar');
    });
    
    // Eliminación (requiere permiso)
    Route::middleware(['permission:gestionar_reprogramaciones'])->group(function () {
        Route::delete('/admin/reprogramaciones/{reprogramacion}', [ReprogramacionController::class, 'destroy'])
            ->name('reprogramaciones.destroy');
    });
    
    // Endpoint AJAX para obtener aulas disponibles
    Route::post('/admin/reprogramaciones/aulas-disponibles', [ReprogramacionController::class, 'aulasDisponibles'])
        ->name('reprogramaciones.aulas-disponibles')
        ->middleware(['permission:gestionar_reprogramaciones']);
});
