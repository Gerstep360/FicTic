<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarreraController;

Route::middleware(['auth'])->group(function () {
    Route::get('/carreras', [CarreraController::class, 'index'])->name('carreras.index');
    Route::get('/carreras/{carrera}', [CarreraController::class, 'show'])->name('carreras.show');

    Route::post('/carreras', [CarreraController::class, 'store'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('carreras.store');

    Route::put('/carreras/{carrera}', [CarreraController::class, 'update'])
        ->middleware('permission:registrar_unidades_academicas')
        ->name('carreras.update');
});
