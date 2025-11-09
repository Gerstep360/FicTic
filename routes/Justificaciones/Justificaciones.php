<?php

use App\Http\Controllers\JustificacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    
    // Rutas para DOCENTES (solicitar justificaciones)
    Route::get('/mis-justificaciones', [JustificacionController::class, 'misJustificaciones'])
         ->name('justificaciones.mis-justificaciones')
         ->middleware('permission:solicitar_justificacion');
    
    Route::get('/justificaciones/crear', [JustificacionController::class, 'create'])
         ->name('justificaciones.create')
         ->middleware('permission:solicitar_justificacion');
    
    Route::post('/justificaciones', [JustificacionController::class, 'store'])
         ->name('justificaciones.store')
         ->middleware('permission:solicitar_justificacion');
    
    // Rutas para COORDINADORES/DIRECTORES (gestionar justificaciones)
    Route::middleware(['permission:gestionar_justificaciones'])->group(function () {
        Route::get('/admin/justificaciones', [JustificacionController::class, 'index'])
             ->name('justificaciones.index');
        
        Route::get('/admin/justificaciones/{justificacion}', [JustificacionController::class, 'show'])
             ->name('justificaciones.show');
        
        Route::post('/admin/justificaciones/{justificacion}/aprobar', [JustificacionController::class, 'aprobar'])
             ->name('justificaciones.aprobar');
        
        Route::post('/admin/justificaciones/{justificacion}/rechazar', [JustificacionController::class, 'rechazar'])
             ->name('justificaciones.rechazar');
        
        Route::delete('/admin/justificaciones/{justificacion}', [JustificacionController::class, 'destroy'])
             ->name('justificaciones.destroy');
    });
});
