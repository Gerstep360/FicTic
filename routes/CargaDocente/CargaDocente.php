<?php

use App\Http\Controllers\CargaDocenteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('cargas-docentes.')->group(function () {
    Route::resource('cargas-docentes', CargaDocenteController::class)
        ->parameters(['cargas-docentes' => 'cargaDocente'])
        ->names([
            'index'   => 'index',
            'create'  => 'create',
            'store'   => 'store',
            'show'    => 'show',
            'edit'    => 'edit',
            'update'  => 'update',
            'destroy' => 'destroy',
        ]);
});
