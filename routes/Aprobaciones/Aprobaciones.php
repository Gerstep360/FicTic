<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AprobacionHorarioController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    
    // Coordinador - Gestión de aprobaciones
    Route::get('/aprobaciones', [AprobacionHorarioController::class, 'index'])
        ->name('aprobaciones.index');
    
    Route::post('/aprobaciones', [AprobacionHorarioController::class, 'store'])
        ->name('aprobaciones.store');
    
    Route::get('/aprobaciones/{aprobacion}', [AprobacionHorarioController::class, 'show'])
        ->name('aprobaciones.show');
    
    Route::post('/aprobaciones/{aprobacion}/enviar-director', [AprobacionHorarioController::class, 'enviarDirector'])
        ->name('aprobaciones.enviar-director');
    
    Route::post('/aprobaciones/{aprobacion}/responder', [AprobacionHorarioController::class, 'responderObservaciones'])
        ->name('aprobaciones.responder');
    
    Route::delete('/aprobaciones/{aprobacion}', [AprobacionHorarioController::class, 'destroy'])
        ->name('aprobaciones.destroy');
    
    // Director - Revisión y aprobación
    Route::get('/aprobaciones/director/pendientes', [AprobacionHorarioController::class, 'pendientesDirector'])
        ->name('aprobaciones.pendientes-director');
    
    Route::post('/aprobaciones/{aprobacion}/aprobar-director', [AprobacionHorarioController::class, 'aprobarDirector'])
        ->name('aprobaciones.aprobar-director');
    
    Route::post('/aprobaciones/{aprobacion}/observar-director', [AprobacionHorarioController::class, 'observarDirector'])
        ->name('aprobaciones.observar-director');
    
    Route::post('/aprobaciones/{aprobacion}/enviar-decano', [AprobacionHorarioController::class, 'enviarDecano'])
        ->name('aprobaciones.enviar-decano');
    
    // Decano - Aprobación final
    Route::get('/aprobaciones/decano/pendientes', [AprobacionHorarioController::class, 'pendientesDecano'])
        ->name('aprobaciones.pendientes-decano');
    
    Route::post('/aprobaciones/{aprobacion}/aprobar-decano', [AprobacionHorarioController::class, 'aprobarDecano'])
        ->name('aprobaciones.aprobar-decano');
    
    Route::post('/aprobaciones/{aprobacion}/observar-decano', [AprobacionHorarioController::class, 'observarDecano'])
        ->name('aprobaciones.observar-decano');
});
