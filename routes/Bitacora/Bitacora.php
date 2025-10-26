<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BitacoraController;

Route::middleware(['auth','permission:ver_reportes'])->group(function () {
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/bitacora/export', [BitacoraController::class, 'export'])->name('bitacora.export');
    Route::get('/bitacora/{bitacora}', [BitacoraController::class, 'show'])->name('bitacora.show');
});
