<?php

use App\Http\Controllers\AsistenciaQrController;
use Illuminate\Support\Facades\Route;

// Rutas para registro de asistencia por QR (requiere permiso registrar_asistencia_qr o Admin DTIC)
Route::middleware(['auth', 'permission:registrar_asistencia_qr|Admin DTIC'])->group(function () {
    // Interfaz principal de escaneo
    Route::get('/asistencia/qr', [AsistenciaQrController::class, 'index'])->name('asistencia-qr.index');
    
    // Endpoint de escaneo (POST con token)
    Route::post('/asistencia/escanear-qr', [AsistenciaQrController::class, 'escanear'])->name('asistencia.escanear-qr');
    
    // Historial de asistencias
    Route::get('/asistencia/historial', [AsistenciaQrController::class, 'historialDia'])->name('asistencia-qr.historial');
    
    // API: Obtener horarios actuales (para modo manual)
    Route::get('/asistencia/horarios-actuales', [AsistenciaQrController::class, 'horariosActuales'])->name('asistencia-qr.horarios-actuales');
});
