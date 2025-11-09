<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\DocenteQrToken;
use App\Models\HorarioClase;
use App\Support\LogsBitacora;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaQrController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:registrar_asistencia_qr|Admin DTIC');
    }
    
    /**
     * Interfaz de escaneo QR
     */
    public function index()
    {
        return view('asistencia-qr.index');
    }
    
    /**
     * Procesar el escaneo del QR y registrar asistencia
     */
    public function escanear(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|size:64',
            'tipo_marca' => 'nullable|in:ENTRADA,SALIDA',
            'id_horario' => 'nullable|exists:horario_clases,id_horario', // Opcional: si se selecciona el horario manualmente
        ]);
        
        $token = $validated['token'];
        $tipoMarca = $validated['tipo_marca'] ?? 'ENTRADA';
        
        // 1. Validar que el token existe y está activo
        $qrToken = DocenteQrToken::where('token', $token)
                                  ->with(['docente', 'gestion'])
                                  ->first();
        
        if (!$qrToken) {
            $this->logBitacora($request, [
                'accion' => 'escanear_qr',
                'modulo' => 'Asistencia QR',
                'tabla_afectada' => 'asistencias',
                'descripcion' => "Intento de escaneo con token inválido: " . substr($token, 0, 16) . "...",
                'metadata' => ['token' => substr($token, 0, 16), 'ip' => $request->ip()],
                'exitoso' => false
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Código QR no válido o no reconocido',
                'tipo' => 'error'
            ], 404);
        }
        
        if (!$qrToken->activo) {
            $this->logBitacora($request, [
                'accion' => 'escanear_qr',
                'modulo' => 'Asistencia QR',
                'tabla_afectada' => 'asistencias',
                'descripcion' => "Intento de escaneo con QR inactivo del docente {$qrToken->docente->name}",
                'metadata' => ['id_docente' => $qrToken->id_docente, 'id_gestion' => $qrToken->id_gestion],
                'exitoso' => false
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Código QR desactivado. Contacte al coordinador.',
                'tipo' => 'warning'
            ], 403);
        }
        
        if (!$qrToken->esta_vigente) {
            $this->logBitacora($request, [
                'accion' => 'escanear_qr',
                'modulo' => 'Asistencia QR',
                'tabla_afectada' => 'asistencias',
                'descripcion' => "Intento de escaneo con QR expirado del docente {$qrToken->docente->name}",
                'metadata' => ['id_docente' => $qrToken->id_docente, 'fecha_expiracion' => $qrToken->fecha_expiracion],
                'exitoso' => false
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Código QR expirado',
                'tipo' => 'warning'
            ], 403);
        }
        
        // 2. Verificar que la gestión del QR está activa
        $now = Carbon::now();
        $gestion = $qrToken->gestion;
        
        if (!$gestion || $now->lt($gestion->fecha_inicio) || $now->gt($gestion->fecha_fin)) {
            return response()->json([
                'success' => false,
                'message' => "❌ Código no válido para la gestión actual. Gestión: {$gestion->nombre}",
                'tipo' => 'warning'
            ], 403);
        }
        
        // 3. Obtener el horario correspondiente al momento actual
        $diaSemana = $now->dayOfWeekIso; // 1=lunes, 7=domingo
        $horaActual = $now->format('H:i:s');
        $fechaHoy = $now->toDateString();
        
        // Si se proporcionó id_horario explícitamente (modo manual), usarlo
        if (!empty($validated['id_horario'])) {
            $horario = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                   ->where('id_horario', $validated['id_horario'])
                                   ->where('id_docente', $qrToken->id_docente)
                                   ->first();
            
            if (!$horario) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ El horario seleccionado no pertenece a este docente',
                    'tipo' => 'error'
                ], 404);
            }
        } else {
            // Modo automático: buscar el horario actual del docente
            $horario = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                   ->where('id_docente', $qrToken->id_docente)
                                   ->where('dia_semana', $diaSemana)
                                   ->whereHas('bloque', function ($query) use ($horaActual) {
                                       $query->where('hora_inicio', '<=', $horaActual)
                                             ->where('hora_fin', '>=', $horaActual);
                                   })
                                   ->first();
            
            if (!$horario) {
                // Buscar el próximo horario del día (tolerancia de 30 minutos antes)
                $horarioProximo = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                              ->where('id_docente', $qrToken->id_docente)
                                              ->where('dia_semana', $diaSemana)
                                              ->whereHas('bloque', function ($query) use ($horaActual) {
                                                  $query->where('hora_inicio', '>', $horaActual)
                                                        ->whereRaw('TIME(hora_inicio) <= ADDTIME(?, "00:30:00")', [$horaActual]);
                                              })
                                              ->orderBy('id_bloque')
                                              ->first();
                
                if ($horarioProximo) {
                    $horario = $horarioProximo;
                } else {
                    $this->logBitacora($request, [
                        'accion' => 'escanear_qr',
                        'modulo' => 'Asistencia QR',
                        'tabla_afectada' => 'asistencias',
                        'descripcion' => "No se encontró horario para el docente {$qrToken->docente->name} en el día y hora actual",
                        'metadata' => ['id_docente' => $qrToken->id_docente, 'dia' => $diaSemana, 'hora' => $horaActual],
                        'exitoso' => false
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => "❌ No hay clases programadas para {$qrToken->docente->name} en este momento",
                        'tipo' => 'info',
                        'docente' => $qrToken->docente->name,
                        'hora_actual' => $now->format('H:i')
                    ], 404);
                }
            }
        }
        
        // 4. Verificar si ya existe un registro de asistencia para este horario hoy
        $asistenciaExistente = Asistencia::where('id_docente', $qrToken->id_docente)
                                         ->where('id_horario', $horario->id_horario)
                                         ->whereDate('fecha_hora', $fechaHoy)
                                         ->where('tipo_marca', $tipoMarca)
                                         ->first();
        
        if ($asistenciaExistente) {
            return response()->json([
                'success' => false,
                'message' => "⚠️ Ya se registró {$tipoMarca} de {$qrToken->docente->name} para esta clase hoy",
                'tipo' => 'warning',
                'docente' => $qrToken->docente->name,
                'materia' => $horario->grupo->materia->nombre,
                'aula' => $horario->aula->codigo,
                'hora_registro' => $asistenciaExistente->fecha_hora->format('H:i:s'),
                'tipo_marca' => $tipoMarca
            ], 409);
        }
        
        // 5. Registrar la asistencia
        $asistencia = Asistencia::create([
            'id_docente' => $qrToken->id_docente,
            'id_horario' => $horario->id_horario,
            'fecha_hora' => $now,
            'tipo_marca' => $tipoMarca,
            'estado' => 'PRESENTE',
        ]);
        
        // 6. Registrar uso del QR token
        $qrToken->registrarUso();
        
        // 7. Bitácora
        $this->logBitacora($request, [
            'accion' => 'escanear_qr',
            'modulo' => 'Asistencia QR',
            'tabla_afectada' => 'asistencias',
            'registro_id' => $asistencia->id_asistencia,
            'descripcion' => "{$tipoMarca} registrada para {$qrToken->docente->name} - {$horario->grupo->materia->nombre} en {$horario->aula->codigo}",
            'id_gestion' => $qrToken->id_gestion,
            'metadata' => [
                'id_docente' => $qrToken->id_docente,
                'id_horario' => $horario->id_horario,
                'aula' => $horario->aula->codigo,
                'materia' => $horario->grupo->materia->nombre,
                'bloque' => $horario->bloque->etiqueta ?? ($horario->bloque->hora_inicio . '-' . $horario->bloque->hora_fin),
                'tipo_marca' => $tipoMarca,
                'escaneado_por' => auth()->user()->name
            ],
            'exitoso' => true
        ]);
        
        // 8. Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => "✅ {$tipoMarca} registrada exitosamente",
            'tipo' => 'success',
            'data' => [
                'docente' => $qrToken->docente->name,
                'materia' => $horario->grupo->materia->nombre,
                'grupo' => $horario->grupo->nombre_grupo,
                'aula' => $horario->aula->codigo,
                'edificio' => $horario->aula->edificio,
                'hora_registro' => $now->format('H:i:s'),
                'fecha' => $now->format('d/m/Y'),
                'bloque' => $horario->bloque->etiqueta ?? ($horario->bloque->hora_inicio . '-' . $horario->bloque->hora_fin),
                'tipo_marca' => $tipoMarca,
                'id_asistencia' => $asistencia->id_asistencia
            ]
        ], 201);
    }
    
    /**
     * Historial de asistencias del día
     */
    public function historialDia(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::now()->toDateString());
        
        $asistencias = Asistencia::with(['docente:id,name,email', 'horario.grupo.materia', 'horario.aula', 'horario.bloque'])
                                  ->whereDate('fecha_hora', $fecha)
                                  ->orderBy('fecha_hora', 'desc')
                                  ->paginate(50);
        
        return view('asistencia-qr.historial', compact('asistencias', 'fecha'));
    }
    
    /**
     * Obtener horarios actuales para selección manual
     */
    public function horariosActuales(Request $request)
    {
        $now = Carbon::now();
        $diaSemana = $now->dayOfWeekIso;
        $horaActual = $now->format('H:i:s');
        
        // Horarios en curso o próximos (dentro de 1 hora)
        $horarios = HorarioClase::with(['docente:id,name', 'grupo.materia', 'aula', 'bloque'])
                                ->where('dia_semana', $diaSemana)
                                ->whereHas('bloque', function ($query) use ($horaActual) {
                                    $query->where(function ($q) use ($horaActual) {
                                        // En curso
                                        $q->where('hora_inicio', '<=', $horaActual)
                                          ->where('hora_fin', '>=', $horaActual);
                                    })->orWhere(function ($q) use ($horaActual) {
                                        // Próximos (1 hora de tolerancia)
                                        $q->where('hora_inicio', '>', $horaActual)
                                          ->whereRaw('TIME(hora_inicio) <= ADDTIME(?, "01:00:00")', [$horaActual]);
                                    });
                                })
                                ->orderBy('id_bloque')
                                ->get();
        
        return response()->json([
            'horarios' => $horarios->map(function ($h) {
                return [
                    'id_horario' => $h->id_horario,
                    'docente' => $h->docente->name,
                    'materia' => $h->grupo->materia->nombre,
                    'grupo' => $h->grupo->nombre_grupo,
                    'aula' => $h->aula->codigo,
                    'bloque' => $h->bloque->etiqueta ?? ($h->bloque->hora_inicio . '-' . $h->bloque->hora_fin),
                    'hora_inicio' => $h->bloque->hora_inicio,
                    'hora_fin' => $h->bloque->hora_fin,
                ];
            })
        ]);
    }
}
