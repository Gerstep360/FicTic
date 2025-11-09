<?php

use App\Http\Controllers\SuplenciaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:gestionar_suplencias'])->group(function () {
    
    // Listado y gestión de suplencias
    Route::get('/admin/suplencias', [SuplenciaController::class, 'index'])
         ->name('suplencias.index');
    
    Route::get('/admin/suplencias/crear', [SuplenciaController::class, 'create'])
         ->name('suplencias.create');
    
    Route::post('/admin/suplencias', [SuplenciaController::class, 'store'])
         ->name('suplencias.store');
    
    Route::get('/admin/suplencias/{suplencia}', [SuplenciaController::class, 'show'])
         ->name('suplencias.show');
    
    Route::delete('/admin/suplencias/{suplencia}', [SuplenciaController::class, 'destroy'])
         ->name('suplencias.destroy');
    
    // AJAX: buscar docentes disponibles
    Route::post('/admin/suplencias/docentes-disponibles', [SuplenciaController::class, 'docentesDisponibles'])
         ->name('suplencias.docentes-disponibles');
});

// Ruta para docentes ver sus suplencias asignadas
Route::middleware(['auth'])->group(function () {
    Route::get('/mis-suplencias', [SuplenciaController::class, 'misSuplencias'])
         ->name('suplencias.mis-suplencias')
         ->middleware('role:Docente');
});
