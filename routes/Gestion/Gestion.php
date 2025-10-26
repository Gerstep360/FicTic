<?php
use App\Http\Controllers\GestionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/gestiones', [GestionController::class, 'index'])->name('gestiones.index');
    Route::get('/gestiones/create', [GestionController::class, 'create']) // <-- Agrega esta línea
        ->middleware('permission:abrir_gestion')
        ->name('gestiones.create');
    Route::get('/gestiones/{gestion}', [GestionController::class, 'show'])->name('gestiones.show');

    // Abrir gestión (solo autoridades con permiso abrir_gestion)
    Route::post('/gestiones', [GestionController::class, 'store'])
        ->middleware('permission:abrir_gestion')
        ->name('gestiones.store');
Route::get('/gestiones/{gestion}/edit', [GestionController::class, 'edit'])
    ->middleware('permission:abrir_gestion')
    ->name('gestiones.edit');
    // Edición básica
    Route::put('/gestiones/{gestion}', [GestionController::class, 'update'])
        ->middleware('permission:abrir_gestion')
        ->name('gestiones.update');
});
