<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\HorarioClase;
use App\Models\User;
use App\Support\LogsBitacora;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaManualController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:asistencia_manual|Admin DTIC');
    }
    
    /**
     * Formulario de registro manual
     */
    public function index()
    {
        // Obtener docentes con rol Docente, Coordinador o Director
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
                        ->orderBy('name')
                        ->get(['id', 'name', 'email']);
        
        return view('asistencia-manual.index', compact('docentes'));
    }
    
    /**
     * Obtener horarios del docente para una fecha específica
     */
    public function horariosDocente(Request $request)
    {
        $validated = $request->validate([
            'id_docente' => 'required|exists:users,id',
            'fecha' => 'required|date',
        ]);
        
        $fecha = Carbon::parse($validated['fecha']);
        $diaSemana = $fecha->dayOfWeekIso; // 1=lunes, 7=domingo
        
        $horarios = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                ->where('id_docente', $validated['id_docente'])
                                ->where('dia_semana', $diaSemana)
                                ->orderBy('id_bloque')
                                ->get();
        
        return response()->json([
            'horarios' => $horarios->map(function ($h) {
                return [
                    'id_horario' => $h->id_horario,
                    'materia' => $h->grupo->materia->nombre,
                    'grupo' => $h->grupo->nombre_grupo,
                    'aula' => $h->aula->codigo,
                    'edificio' => $h->aula->edificio,
                    'bloque' => $h->bloque->etiqueta ?? ($h->bloque->hora_inicio . '-' . $h->bloque->hora_fin),
                    'hora_inicio' => $h->bloque->hora_inicio,
                    'hora_fin' => $h->bloque->hora_fin,
                ];
            })
        ]);
    }
    
    /**
     * Registrar asistencia manual
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_docente' => 'required|exists:users,id',
            'id_horario' => 'required|exists:horario_clases,id_horario',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'tipo_marca' => 'required|in:ENTRADA,SALIDA',
            'estado' => 'required|in:PRESENTE,FALTA,TARDANZA',
            'observacion' => 'required|string|min:10|max:500',
        ]);
        
        // Construir fecha_hora
        $fechaHora = Carbon::parse($validated['fecha'] . ' ' . $validated['hora']);
        
        // Verificar que el horario pertenece al docente
        $horario = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                               ->where('id_horario', $validated['id_horario'])
                               ->where('id_docente', $validated['id_docente'])
                               ->firstOrFail();
        
        // Verificar si ya existe un registro para este horario y fecha
        $existente = Asistencia::where('id_docente', $validated['id_docente'])
                               ->where('id_horario', $validated['id_horario'])
                               ->whereDate('fecha_hora', $validated['fecha'])
                               ->where('tipo_marca', $validated['tipo_marca'])
                               ->first();
        
        if ($existente) {
            return back()->with('error', 'Ya existe un registro de ' . $validated['tipo_marca'] . ' para este horario y fecha.');
        }
        
        // Crear registro de asistencia manual
        $asistencia = Asistencia::create([
            'id_docente' => $validated['id_docente'],
            'id_horario' => $validated['id_horario'],
            'fecha_hora' => $fechaHora,
            'tipo_marca' => $validated['tipo_marca'],
            'estado' => $validated['estado'],
            'es_manual' => true,
            'registrado_por' => auth()->id(),
            'observacion' => $validated['observacion'],
        ]);
        
        // Obtener info del docente
        $docente = User::findOrFail($validated['id_docente']);
        
        // Bitácora
        $this->logBitacora(
            accion: 'registro_manual',
            tabla_afectada: 'asistencias',
            registro_id: $asistencia->id_asistencia,
            descripcion: "Registro manual de asistencia para {$docente->name} - {$horario->grupo->materia->nombre} en {$horario->aula->codigo}",
            metadata: [
                'id_docente' => $docente->id,
                'docente' => $docente->name,
                'id_horario' => $horario->id_horario,
                'materia' => $horario->grupo->materia->nombre,
                'aula' => $horario->aula->codigo,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'tipo_marca' => $validated['tipo_marca'],
                'estado' => $validated['estado'],
                'observacion' => $validated['observacion'],
                'registrado_por' => auth()->user()->name
            ],
            exitoso: true
        );
        
        return redirect()
            ->route('asistencia-manual.index')
            ->with('success', "Asistencia manual registrada exitosamente para {$docente->name}");
    }
    
    /**
     * Listado de asistencias manuales (para auditoría)
     */
    public function listado(Request $request)
    {
        $query = Asistencia::with(['docente:id,name,email', 'horario.grupo.materia', 'horario.aula', 'horario.bloque', 'registrador:id,name'])
                           ->where('es_manual', true)
                           ->orderBy('fecha_hora', 'desc');
        
        // Filtros opcionales
        if ($request->filled('id_docente')) {
            $query->where('id_docente', $request->id_docente);
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }
        
        $asistencias = $query->paginate(30);
        
        // Obtener docentes para el filtro
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
                        ->orderBy('name')
                        ->get(['id', 'name']);
        
        return view('asistencia-manual.listado', compact('asistencias', 'docentes'));
    }
    
    /**
     * Editar/corregir una asistencia manual
     */
    public function edit(Asistencia $asistencia)
    {
        // Solo permitir editar registros manuales
        if (!$asistencia->es_manual) {
            return redirect()
                ->route('asistencia-manual.listado')
                ->with('error', 'Solo se pueden editar registros manuales');
        }
        
        $asistencia->load(['docente', 'horario.grupo.materia', 'horario.aula', 'horario.bloque']);
        
        return view('asistencia-manual.edit', compact('asistencia'));
    }
    
    /**
     * Actualizar asistencia manual
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        // Solo permitir editar registros manuales
        if (!$asistencia->es_manual) {
            return back()->with('error', 'Solo se pueden editar registros manuales');
        }
        
        $validated = $request->validate([
            'estado' => 'required|in:PRESENTE,FALTA,TARDANZA',
            'observacion' => 'required|string|min:10|max:500',
        ]);
        
        $estadoAnterior = $asistencia->estado;
        $observacionAnterior = $asistencia->observacion;
        
        $asistencia->update([
            'estado' => $validated['estado'],
            'observacion' => $validated['observacion'] . "\n\n[Editado por " . auth()->user()->name . " el " . now()->format('d/m/Y H:i') . "]",
        ]);
        
        // Bitácora de edición
        $this->logBitacora(
            accion: 'editar_asistencia_manual',
            tabla_afectada: 'asistencias',
            registro_id: $asistencia->id_asistencia,
            descripcion: "Corrección de asistencia manual de {$asistencia->docente->name}",
            metadata: [
                'id_docente' => $asistencia->id_docente,
                'docente' => $asistencia->docente->name,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $validated['estado'],
                'observacion_anterior' => $observacionAnterior,
                'observacion_nueva' => $validated['observacion'],
                'editado_por' => auth()->user()->name
            ],
            exitoso: true
        );
        
        return redirect()
            ->route('asistencia-manual.listado')
            ->with('success', 'Asistencia corregida exitosamente');
    }
    
    /**
     * Eliminar asistencia manual (solo si fue error)
     */
    public function destroy(Request $request, Asistencia $asistencia)
    {
        // Solo permitir eliminar registros manuales
        if (!$asistencia->es_manual) {
            return back()->with('error', 'Solo se pueden eliminar registros manuales');
        }
        
        $request->validate([
            'motivo_eliminacion' => 'required|string|min:10|max:200',
        ]);
        
        $docente = $asistencia->docente->name;
        $fecha = $asistencia->fecha_hora->format('d/m/Y H:i');
        
        // Bitácora antes de eliminar
        $this->logBitacora(
            accion: 'eliminar_asistencia_manual',
            tabla_afectada: 'asistencias',
            registro_id: $asistencia->id_asistencia,
            descripcion: "Eliminación de asistencia manual de {$docente} del {$fecha}",
            metadata: [
                'id_docente' => $asistencia->id_docente,
                'docente' => $docente,
                'fecha_hora' => $fecha,
                'estado' => $asistencia->estado,
                'tipo_marca' => $asistencia->tipo_marca,
                'observacion_original' => $asistencia->observacion,
                'motivo_eliminacion' => $request->motivo_eliminacion,
                'eliminado_por' => auth()->user()->name
            ],
            exitoso: true
        );
        
        $asistencia->delete();
        
        return redirect()
            ->route('asistencia-manual.listado')
            ->with('success', 'Registro eliminado exitosamente');
    }
}
