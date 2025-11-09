<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaManualController;

/*
|--------------------------------------------------------------------------
| Rutas de Asistencia Manual (CU-21 Contingencia)
|--------------------------------------------------------------------------
| Sistema de registro manual de asistencia como contingencia ante fallas
| del escáner QR o para correcciones posteriores. Requiere justificación
| y registra completa trazabilidad en bitácora.
*/

Route::middleware(['auth', 'permission:asistencia_manual|Admin DTIC'])->group(function () {
    
    // Formulario de registro manual
    Route::get('/asistencia/manual', [AsistenciaManualController::class, 'index'])
        ->name('asistencia-manual.index');
    
    // Endpoint AJAX para obtener horarios del docente según fecha
    Route::get('/asistencia/manual/horarios-docente', [AsistenciaManualController::class, 'horariosDocente'])
        ->name('asistencia-manual.horarios-docente');
    
    // Registrar asistencia manual
    Route::post('/asistencia/manual', [AsistenciaManualController::class, 'store'])
        ->name('asistencia-manual.store');
    
    // Listado/auditoría de registros manuales
    Route::get('/asistencia/manual/listado', [AsistenciaManualController::class, 'listado'])
        ->name('asistencia-manual.listado');
    
    // Formulario de edición/corrección
    Route::get('/asistencia/manual/{asistencia}/edit', [AsistenciaManualController::class, 'edit'])
        ->name('asistencia-manual.edit');
    
    // Actualizar asistencia manual (solo campos estado y observacion)
    Route::patch('/asistencia/manual/{asistencia}', [AsistenciaManualController::class, 'update'])
        ->name('asistencia-manual.update');
    
    // Eliminar asistencia manual (requiere motivo_eliminacion)
    Route::delete('/asistencia/manual/{asistencia}', [AsistenciaManualController::class, 'destroy'])
        ->name('asistencia-manual.destroy');
});

