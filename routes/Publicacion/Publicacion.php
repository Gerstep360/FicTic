<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicacionHorarioController;

// ====================================
// RUTAS DE ADMINISTRACIÓN (requieren autenticación y permisos)
// ====================================
Route::middleware(['auth'])->prefix('admin/publicacion')->name('publicacion.')->group(function () {
    
    // Listado de gestiones para publicar
    Route::get('/', [PublicacionHorarioController::class, 'index'])
        ->middleware('permission:publicar_horarios|Admin DTIC')
        ->name('index');
    
    // Publicar una gestión
    Route::post('/{gestion}/publicar', [PublicacionHorarioController::class, 'publicar'])
        ->middleware('permission:publicar_horarios|Admin DTIC')
        ->name('publicar');
    
    // Despublicar una gestión
    Route::delete('/{gestion}/despublicar', [PublicacionHorarioController::class, 'despublicar'])
        ->middleware('permission:publicar_horarios|Admin DTIC')
        ->name('despublicar');
});

// ====================================
// RUTAS PÚBLICAS (sin autenticación)
// ====================================
Route::prefix('horarios')->name('publicacion.')->group(function () {
    
    // Vista por docente
    Route::get('/docente', [PublicacionHorarioController::class, 'porDocente'])
        ->name('por-docente');
    
    // Vista por grupo
    Route::get('/grupo', [PublicacionHorarioController::class, 'porGrupo'])
        ->name('por-grupo');
    
    // Vista por aula
    Route::get('/aula', [PublicacionHorarioController::class, 'porAula'])
        ->name('por-aula');
    
    // Maestro de oferta
    Route::get('/maestro/{gestion}', [PublicacionHorarioController::class, 'maestroOferta'])
        ->name('maestro');
    
    // ====================================
    // EXPORTACIONES PDF (públicas)
    // ====================================
    
    // PDF por docente
    Route::get('/docente/{gestion}/{docente}/pdf', [PublicacionHorarioController::class, 'pdfDocente'])
        ->name('pdf-docente');
    
    // PDF por grupo
    Route::get('/grupo/{grupo}/pdf', [PublicacionHorarioController::class, 'pdfGrupo'])
        ->name('pdf-grupo');
    
    // PDF por aula
    Route::get('/aula/{gestion}/{aula}/pdf', [PublicacionHorarioController::class, 'pdfAula'])
        ->name('pdf-aula');
    
    // PDF maestro de oferta
    Route::get('/maestro/{gestion}/pdf', [PublicacionHorarioController::class, 'pdfMaestro'])
        ->name('pdf-maestro');
});
