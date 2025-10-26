<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;

Route::middleware(['auth', 'permission:definir_roles_perfiles'])
    ->prefix('admin')
    ->name('roles.')
    ->group(function () {
        // 1) Rutas estáticas ANTES del resource
        Route::get('roles/create', [RolesController::class, 'create'])->name('create');
        Route::get('roles/{role}/edit', [RolesController::class, 'edit'])
            ->whereNumber('role') // 2) blindaje
            ->name('edit');

        // 3) Resource después + blindaje del parámetro
        Route::resource('roles', RolesController::class)
            ->except(['create', 'edit'])
            ->whereNumber('role')
            ->names([
                'index'   => 'index',
                'show'    => 'show',
                'store'   => 'store',
                'update'  => 'update',
                'destroy' => 'destroy',
            ]);
    });
