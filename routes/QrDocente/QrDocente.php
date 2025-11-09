<?php

use App\Http\Controllers\QrDocenteController;
use App\Models\DocenteQrToken;
use Illuminate\Support\Facades\Route;

// Binding del modelo DocenteQrToken
Route::bind('token', function ($value) {
    return DocenteQrToken::findOrFail($value);
});

// Rutas para docentes (self-service - solo requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/mi-qr', [QrDocenteController::class, 'miQr'])->name('qr-docente.mi-qr');
    Route::get('/mi-qr/descargar', [QrDocenteController::class, 'descargarMiQr'])->name('qr-docente.descargar-mi-qr');
});

// Rutas administrativas (requiere permiso generar_qr_docente o Admin DTIC)
Route::middleware(['auth', 'permission:generar_qr_docente|Admin DTIC'])->prefix('admin/qr-docente')->group(function () {
    Route::get('/', [QrDocenteController::class, 'index'])->name('qr-docente.index');
    Route::post('/masivo', [QrDocenteController::class, 'generarMasivo'])->name('qr-docente.generar-masivo');
    Route::post('/generar', [QrDocenteController::class, 'generar'])->name('qr-docente.generar');
    Route::get('/estadisticas', [QrDocenteController::class, 'estadisticas'])->name('qr-docente.estadisticas');
    
    // Rutas con route model binding
    Route::get('/{token}', [QrDocenteController::class, 'ver'])->name('qr-docente.ver');
    Route::patch('/{token}/activar', [QrDocenteController::class, 'activar'])->name('qr-docente.activar');
    Route::patch('/{token}/desactivar', [QrDocenteController::class, 'desactivar'])->name('qr-docente.desactivar');
    Route::patch('/{token}/regenerar', [QrDocenteController::class, 'regenerar'])->name('qr-docente.regenerar');
    Route::get('/{token}/pdf', [QrDocenteController::class, 'descargarPdf'])->name('qr-docente.descargar-pdf');
    Route::get('/{token}/png', [QrDocenteController::class, 'descargarImagen'])->name('qr-docente.descargar-imagen');
});
