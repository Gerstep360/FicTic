<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosImportController;

Route::middleware(['auth','permission:importar_usuarios'])
    ->prefix('admin/usuarios')
    ->name('usuarios.import.')
    ->group(function () {
        // Form de importación
        Route::get('importar', [UsuariosImportController::class, 'create'])->name('create');
        // Descarga plantilla CSV (cabeceras en español)
        Route::get('importar/plantilla', [UsuariosImportController::class, 'plantilla'])->name('plantilla');
        // Proceso de importación
        Route::post('importar', [UsuariosImportController::class, 'store'])->name('store');
    });
