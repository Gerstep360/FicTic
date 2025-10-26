<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware(['auth'])->prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    
    // Gestión de roles (requiere permiso - puede que necesites ajustar el nombre del permiso)
    Route::post('/{user}/roles', [UserController::class, 'updateRoles'])->name('updateRoles');
});
