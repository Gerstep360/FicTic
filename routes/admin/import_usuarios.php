<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosImportController;

Route::middleware(['auth','permission:importar_usuarios'])
    ->prefix('admin/usuarios')->name('usuarios.import.')
    ->group(function () {
        Route::get('importar', [UsuariosImportController::class, 'create'])->name('create');
        Route::get('importar/plantilla', [UsuariosImportController::class, 'plantilla'])->name('plantilla'); // ahora XLSX
        Route::post('importar', [UsuariosImportController::class, 'store'])->name('store');
    });

