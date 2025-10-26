<?php  
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AulaController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Catálogo de Aulas
    Route::get('aulas',                [AulaController::class, 'index'])
        ->middleware('permission:ver_aulas|gestionar_aulas')
        ->name('aulas.index');

    Route::get('aulas/create',         [AulaController::class, 'create'])
        ->middleware('permission:crear_aulas|gestionar_aulas')
        ->name('aulas.create');

    Route::post('aulas',               [AulaController::class, 'store'])
        ->middleware('permission:crear_aulas|gestionar_aulas')
        ->name('aulas.store');

    Route::get('aulas/{aula}/edit',    [AulaController::class, 'edit'])
        ->middleware('permission:editar_aulas|gestionar_aulas')
        ->name('aulas.edit');

    Route::put('aulas/{aula}',         [AulaController::class, 'update'])
        ->middleware('permission:editar_aulas|gestionar_aulas')
        ->name('aulas.update');

    Route::delete('aulas/{aula}',      [AulaController::class, 'destroy'])
        ->middleware('permission:eliminar_aulas|gestionar_aulas')
        ->name('aulas.destroy');

    Route::post('aulas/{id_aula}/restore', [AulaController::class, 'restore'])
        ->middleware('permission:restaurar_aulas|gestionar_aulas')
        ->name('aulas.restore');
});
