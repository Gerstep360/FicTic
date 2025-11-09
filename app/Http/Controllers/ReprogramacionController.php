<?php

namespace App\Http\Controllers;

use App\Models\Reprogramacion;
use App\Models\HorarioClase;
use App\Models\Aula;
use App\Models\User;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReprogramacionController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:gestionar_reprogramaciones')->except(['index', 'show']);
    }
    
    /**
     * Listado de reprogramaciones
     */
    public function index(Request $request)
    {
        $query = Reprogramacion::with([
            'horarioOriginal.grupo.materia',
            'horarioOriginal.aula',
            'horarioOriginal.docente',
            'aulaNueva',
            'solicitante',
            'aprobador'
        ])->orderBy('fecha_solicitud', 'desc');
        
        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_original', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_original', '<=', $request->fecha_hasta);
        }
        
        $reprogramaciones = $query->paginate(20);
        
        return view('reprogramaciones.index', compact('reprogramaciones'));
    }
    
    /**
     * Detalle de reprogramación
     */
    public function show(Reprogramacion $reprogramacion)
    {
        $reprogramacion->load([
            'horarioOriginal.grupo.materia',
            'horarioOriginal.aula',
            'horarioOriginal.bloque',
            'horarioOriginal.docente',
            'aulaNueva',
            'solicitante',
            'aprobador'
        ]);
        
        return view('reprogramaciones.show', compact('reprogramacion'));
    }
    
    /**
     * Formulario para crear reprogramación
     */
    public function create()
    {
        // Obtener horarios activos
        $horarios = HorarioClase::with(['grupo.materia', 'docente', 'aula', 'bloque'])
                                ->whereHas('grupo.gestion', function($q) {
                                    $q->where('publicada', 1);
                                })
                                ->orderBy('dia_semana')
                                ->get();
        
        $aulas = Aula::orderBy('codigo')->get();
        
        return view('reprogramaciones.create', compact('horarios', 'aulas'));
    }
    
    /**
     * Guardar reprogramación
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_horario_original' => 'required|exists:horario_clases,id_horario',
            'fecha_original' => 'required|date',
            'tipo' => 'required|in:CAMBIO_AULA,CAMBIO_FECHA,AMBOS',
            'id_aula_nueva' => 'nullable|exists:aulas,id_aula|required_if:tipo,CAMBIO_AULA,AMBOS',
            'fecha_nueva' => 'nullable|date|after_or_equal:today|required_if:tipo,CAMBIO_FECHA,AMBOS',
            'motivo' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:500',
        ]);
        
        // Validar disponibilidad del aula nueva si es cambio de aula
        if (in_array($validated['tipo'], ['CAMBIO_AULA', 'AMBOS'])) {
            $horarioOriginal = HorarioClase::findOrFail($validated['id_horario_original']);
            $fechaValidar = $validated['tipo'] === 'AMBOS' ? $validated['fecha_nueva'] : $validated['fecha_original'];
            
            $conflicto = $this->verificarDisponibilidadAula(
                $validated['id_aula_nueva'],
                $horarioOriginal->dia_semana,
                $horarioOriginal->id_bloque,
                $fechaValidar
            );
            
            if ($conflicto) {
                return back()->withErrors(['id_aula_nueva' => 'El aula no está disponible en ese horario.'])->withInput();
            }
        }
        
        DB::beginTransaction();
        try {
            $reprogramacion = Reprogramacion::create([
                'id_horario_original' => $validated['id_horario_original'],
                'fecha_original' => $validated['fecha_original'],
                'id_aula_nueva' => $validated['id_aula_nueva'] ?? null,
                'fecha_nueva' => $validated['fecha_nueva'] ?? null,
                'tipo' => $validated['tipo'],
                'motivo' => $validated['motivo'],
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => 'PENDIENTE',
                'solicitado_por' => Auth::id(),
                'fecha_solicitud' => now(),
            ]);
            
            // Log en bitácora
            $this->logBitacora($request, [
                'accion' => 'crear',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $reprogramacion->id_reprogramacion,
                'descripcion' => "Reprogramación solicitada: {$validated['tipo']} - Motivo: {$validated['motivo']}",
                'exitoso' => true,
            ]);
            
            DB::commit();
            
            return redirect()->route('reprogramaciones.index')
                           ->with('success', 'Reprogramación solicitada exitosamente. Pendiente de aprobación.');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->logBitacora($request, [
                'accion' => 'crear',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'descripcion' => "Error al crear reprogramación: {$e->getMessage()}",
                'exitoso' => false,
            ]);
            
            return back()->withErrors(['error' => 'Error al crear la reprogramación.'])->withInput();
        }
    }
    
    /**
     * Aprobar reprogramación
     */
    public function aprobar(Request $request, Reprogramacion $reprogramacion)
    {
        if ($reprogramacion->estado !== 'PENDIENTE') {
            return back()->withErrors(['error' => 'Solo se pueden aprobar reprogramaciones pendientes.']);
        }
        
        $validated = $request->validate([
            'observaciones' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        try {
            $cambiosAntes = $reprogramacion->toArray();
            
            $reprogramacion->update([
                'estado' => 'APROBADA',
                'aprobado_por' => Auth::id(),
                'fecha_aprobacion' => now(),
                'observaciones' => $validated['observaciones'] ?? $reprogramacion->observaciones,
            ]);
            
            // Log en bitácora
            $this->logBitacora($request, [
                'accion' => 'aprobar',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $reprogramacion->id_reprogramacion,
                'descripcion' => "Reprogramación aprobada - Tipo: {$reprogramacion->tipo}",
                'exitoso' => true,
                'cambios_antes' => $cambiosAntes,
                'cambios_despues' => $reprogramacion->toArray(),
            ]);
            
            DB::commit();
            
            // TODO: Aquí se podría enviar notificaciones a los afectados
            // Notificar al docente, estudiantes del grupo, etc.
            
            return redirect()->route('reprogramaciones.show', $reprogramacion)
                           ->with('success', 'Reprogramación aprobada exitosamente.');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->logBitacora($request, [
                'accion' => 'aprobar',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $reprogramacion->id_reprogramacion,
                'descripcion' => "Error al aprobar reprogramación: {$e->getMessage()}",
                'exitoso' => false,
            ]);
            
            return back()->withErrors(['error' => 'Error al aprobar la reprogramación.']);
        }
    }
    
    /**
     * Rechazar reprogramación
     */
    public function rechazar(Request $request, Reprogramacion $reprogramacion)
    {
        if ($reprogramacion->estado !== 'PENDIENTE') {
            return back()->withErrors(['error' => 'Solo se pueden rechazar reprogramaciones pendientes.']);
        }
        
        $validated = $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        try {
            $cambiosAntes = $reprogramacion->toArray();
            
            $reprogramacion->update([
                'estado' => 'RECHAZADA',
                'aprobado_por' => Auth::id(),
                'fecha_aprobacion' => now(),
                'observaciones' => $validated['observaciones'],
            ]);
            
            // Log en bitácora
            $this->logBitacora($request, [
                'accion' => 'rechazar',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $reprogramacion->id_reprogramacion,
                'descripcion' => "Reprogramación rechazada - Motivo: {$validated['observaciones']}",
                'exitoso' => true,
                'cambios_antes' => $cambiosAntes,
                'cambios_despues' => $reprogramacion->toArray(),
            ]);
            
            DB::commit();
            
            return redirect()->route('reprogramaciones.show', $reprogramacion)
                           ->with('success', 'Reprogramación rechazada.');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->logBitacora($request, [
                'accion' => 'rechazar',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $reprogramacion->id_reprogramacion,
                'descripcion' => "Error al rechazar reprogramación: {$e->getMessage()}",
                'exitoso' => false,
            ]);
            
            return back()->withErrors(['error' => 'Error al rechazar la reprogramación.']);
        }
    }
    
    /**
     * Eliminar reprogramación (solo pendientes)
     */
    public function destroy(Request $request, Reprogramacion $reprogramacion)
    {
        if ($reprogramacion->estado !== 'PENDIENTE') {
            return back()->withErrors(['error' => 'Solo se pueden eliminar reprogramaciones pendientes.']);
        }
        
        DB::beginTransaction();
        try {
            $datosReprogramacion = $reprogramacion->toArray();
            
            $reprogramacion->delete();
            
            // Log en bitácora
            $this->logBitacora($request, [
                'accion' => 'eliminar',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $datosReprogramacion['id_reprogramacion'],
                'descripcion' => "Reprogramación eliminada - Tipo: {$datosReprogramacion['tipo']}",
                'exitoso' => true,
                'cambios_antes' => $datosReprogramacion,
            ]);
            
            DB::commit();
            
            return redirect()->route('reprogramaciones.index')
                           ->with('success', 'Reprogramación eliminada exitosamente.');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->logBitacora($request, [
                'accion' => 'eliminar',
                'modulo' => 'Reprogramaciones',
                'tabla_afectada' => 'reprogramaciones',
                'registro_id' => $reprogramacion->id_reprogramacion,
                'descripcion' => "Error al eliminar reprogramación: {$e->getMessage()}",
                'exitoso' => false,
            ]);
            
            return back()->withErrors(['error' => 'Error al eliminar la reprogramación.']);
        }
    }
    
    /**
     * Endpoint AJAX: Obtener aulas disponibles
     */
    public function aulasDisponibles(Request $request)
    {
        $validated = $request->validate([
            'id_horario' => 'required|exists:horario_clases,id_horario',
            'fecha' => 'required|date',
        ]);
        
        $horario = HorarioClase::findOrFail($validated['id_horario']);
        
        // Obtener todas las aulas
        $todasAulas = Aula::orderBy('codigo')->get();
        
        // Filtrar aulas disponibles
        $aulasDisponibles = $todasAulas->filter(function($aula) use ($horario, $validated) {
            return !$this->verificarDisponibilidadAula(
                $aula->id_aula,
                $horario->dia_semana,
                $horario->id_bloque,
                $validated['fecha']
            );
        })->values();
        
        return response()->json([
            'success' => true,
            'aulas' => $aulasDisponibles->map(function($aula) {
                return [
                    'id_aula' => $aula->id_aula,
                    'codigo' => $aula->codigo,
                    'tipo' => $aula->tipo,
                    'capacidad' => $aula->capacidad,
                    'edificio' => $aula->edificio,
                ];
            }),
        ]);
    }
    
    /**
     * Verificar si un aula está disponible en un horario específico
     */
    private function verificarDisponibilidadAula($idAula, $diaSemana, $idBloque, $fecha)
    {
        // Verificar conflictos en horario_clases (ocupación regular)
        $ocupacionRegular = HorarioClase::where('id_aula', $idAula)
                                        ->where('dia_semana', $diaSemana)
                                        ->where('id_bloque', $idBloque)
                                        ->exists();
        
        if ($ocupacionRegular) {
            return true; // Hay conflicto
        }
        
        // Verificar reprogramaciones aprobadas para esa fecha
        $reprogramacionConflicto = Reprogramacion::where('estado', 'APROBADA')
                                                 ->where('id_aula_nueva', $idAula)
                                                 ->where('fecha_nueva', $fecha)
                                                 ->whereHas('horarioOriginal', function($q) use ($diaSemana, $idBloque) {
                                                     $q->where('dia_semana', $diaSemana)
                                                       ->where('id_bloque', $idBloque);
                                                 })
                                                 ->exists();
        
        return $reprogramacionConflicto; // true = hay conflicto, false = disponible
    }
}
