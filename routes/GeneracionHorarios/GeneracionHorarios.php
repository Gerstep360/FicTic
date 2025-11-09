<?php

use App\Http\Controllers\GeneracionHorarioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('generacion-horarios', GeneracionHorarioController::class)->names('generacion-horarios');
    Route::post('generacion-horarios/{generacionHorario}/aplicar', [GeneracionHorarioController::class, 'aplicar'])
        ->name('generacion-horarios.aplicar');
    Route::get('generacion-horarios/{generacionHorario}/pdf', [GeneracionHorarioController::class, 'pdf'])
        ->name('generacion-horarios.pdf');
});
