<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;

/*
|--------------------------------------------------------------------------
| CU-24: Reportería y Descargas (PDF/Excel)
|--------------------------------------------------------------------------
| Generación de informes consolidados y exportación de datos
*/

Route::middleware(['auth', 'permission:ver_reportes'])->group(function () {
    
    // Vista principal de reportes
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');
    
    // Reportes de Horarios
    Route::get('/reportes/horario-docente', [ReporteController::class, 'horarioDocente'])
        ->name('reportes.horario-docente');
    
    Route::get('/reportes/horario-grupo', [ReporteController::class, 'horarioGrupo'])
        ->name('reportes.horario-grupo');
    
    Route::get('/reportes/horario-aula', [ReporteController::class, 'horarioAula'])
        ->name('reportes.horario-aula');
    
    // Reportes de Asistencia
    Route::get('/reportes/asistencia-docente', [ReporteController::class, 'asistenciaDocente'])
        ->name('reportes.asistencia-docente');
    
    Route::get('/reportes/asistencia-carrera', [ReporteController::class, 'asistenciaCarrera'])
        ->name('reportes.asistencia-carrera');
    
    // Reporte de Ocupación de Aulas
    Route::get('/reportes/ocupacion-aulas', [ReporteController::class, 'ocupacionAulas'])
        ->name('reportes.ocupacion-aulas');
});
