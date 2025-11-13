<?php

namespace App\Http\Controllers;

use App\Models\Suplencia;
use App\Models\Justificacion;
use App\Models\User;
use App\Models\HorarioClase;
use App\Models\DocenteExterno;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SuplenciaController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:gestionar_suplencias');
    }
    
    /**
     * Listado de suplencias
     */
    public function index(Request $request)
    {
        $query = Suplencia::with(['docenteAusente', 'docenteSuplente', 'docenteExterno', 'horario.grupo.materia', 'horario.aula'])
                          ->orderBy('fecha_clase', 'desc');
        
        // Filtros
        if ($request->filled('id_docente_ausente')) {
            $query->where('id_docente_ausente', $request->id_docente_ausente);
        }
        
        if ($request->filled('id_docente_suplente')) {
            $query->where('id_docente_suplente', $request->id_docente_suplente);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_clase', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_clase', '<=', $request->fecha_hasta);
        }
        
        $suplencias = $query->paginate(20);
        
        // Lista de docentes para filtro
        $docentes = User::role(['Docente'])
                       ->orderBy('name')
                       ->get(['id', 'name']);
        
        return view('suplencias.index', compact('suplencias', 'docentes'));
    }
    
    /**
     * Mostrar detalle de suplencia
     */
    public function show(Suplencia $suplencia)
    {
        $suplencia->load(['docenteAusente', 'docenteSuplente', 'docenteExterno', 'horario.grupo.materia', 'horario.aula', 'horario.bloque']);
        
        return view('suplencias.show', compact('suplencia'));
    }
    
    /**
     * Formulario para crear suplencia
     */
    public function create(Request $request)
    {
        // Puede venir desde una justificación aprobada
        $justificacion = null;
        if ($request->filled('justificacion')) {
            $justificacion = Justificacion::with(['docente'])
                                          ->where('id_justif', $request->justificacion)
                                          ->where('estado', 'APROBADA')
                                          ->first();
        }
        
        // Horarios del docente ausente (si viene de justificación o del old input)
        $horarios = collect();
        $idDocenteAusente = $justificacion ? $justificacion->id_docente : old('id_docente_ausente');
        
        if ($idDocenteAusente) {
            $horarios = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                    ->where('id_docente', $idDocenteAusente)
                                    ->get();
        }
        
        // Lista de TODOS los docentes para seleccionar al ausente
        $todosDocentes = User::role(['Docente'])
                             ->orderBy('name')
                             ->get(['id', 'name']);
        
        // Docentes disponibles para suplir (excluir al docente ausente)
        $docentes = User::role(['Docente'])
                       ->where('id', '!=', $idDocenteAusente ?? 0)
                       ->orderBy('name')
                       ->get(['id', 'name']);
        
        return view('suplencias.create', compact('justificacion', 'horarios', 'todosDocentes', 'docentes'));
    }
    
    /**
     * Guardar nueva suplencia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_docente_ausente' => 'required|exists:users,id',
            'id_docente_suplente' => 'required|string',
            'id_horario' => 'required|exists:horario_clases,id_horario',
            'fecha_clase' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
            // Campos para nuevo docente externo
            'nombre_completo_externo' => 'required_if:id_docente_suplente,nuevo_externo|nullable|string|max:255',
            'especialidad_externo' => 'nullable|string|max:100',
            'telefono_externo' => 'nullable|string|max:20',
            'email_externo' => 'nullable|email|max:100',
            'observaciones_externo' => 'nullable|string',
        ], [
            'id_docente_ausente.required' => 'El campo Docente Ausente es obligatorio.',
            'id_docente_ausente.exists' => 'El docente ausente seleccionado no es válido.',
            'id_docente_suplente.required' => 'El campo Docente Suplente es obligatorio.',
            'nombre_completo_externo.required_if' => 'Debe ingresar el nombre del docente externo.',
            'id_horario.required' => 'El campo Horario de la Clase es obligatorio.',
            'id_horario.exists' => 'El horario seleccionado no es válido.',
            'fecha_clase.required' => 'El campo Fecha de la Clase es obligatorio.',
            'fecha_clase.date' => 'El campo Fecha de la Clase debe ser una fecha válida.',
            'observaciones.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ]);
        
        $idDocenteSuplente = null;
        $idDocenteExterno = null;
        
        // Determinar el tipo de suplente
        if ($validated['id_docente_suplente'] === 'nuevo_externo') {
            // Registrar nuevo docente externo
            $docenteExterno = DocenteExterno::create([
                'nombre_completo' => $validated['nombre_completo_externo'],
                'especialidad' => $validated['especialidad_externo'] ?? null,
                'telefono' => $validated['telefono_externo'] ?? null,
                'email' => $validated['email_externo'] ?? null,
                'observaciones' => $validated['observaciones_externo'] ?? null,
                'activo' => true,
            ]);
            $idDocenteExterno = $docenteExterno->id_docente_externo;
            
        } elseif (str_starts_with($validated['id_docente_suplente'], 'ext_')) {
            // Docente externo existente
            $idDocenteExterno = (int) str_replace('ext_', '', $validated['id_docente_suplente']);
            
            if (!DocenteExterno::where('id_docente_externo', $idDocenteExterno)->exists()) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', '❌ El docente externo seleccionado no es válido');
            }
            
        } else {
            // Docente interno del sistema
            $idDocenteSuplente = (int) $validated['id_docente_suplente'];
            
            if (!User::where('id', $idDocenteSuplente)->exists()) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', '❌ El docente suplente seleccionado no es válido');
            }
            
            if ($idDocenteSuplente == $validated['id_docente_ausente']) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', '❌ El docente suplente debe ser diferente al docente ausente');
            }
        }
        
        // Verificar que el horario pertenece al docente ausente
        $horario = HorarioClase::where('id_horario', $validated['id_horario'])
                               ->where('id_docente', $validated['id_docente_ausente'])
                               ->firstOrFail();
        
        // Verificar que no exista ya una suplencia para ese horario y fecha
        // Verificar que no exista ya una suplencia para ese horario y fecha
        $existe = Suplencia::where('id_horario', $validated['id_horario'])
                          ->where('fecha_clase', $validated['fecha_clase'])
                          ->exists();
        
        if ($existe) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', '❌ Ya existe una suplencia registrada para ese horario y fecha');
        }
        
        // Solo verificar conflicto si es docente INTERNO
        if ($idDocenteSuplente) {
            $diaSemana = Carbon::parse($validated['fecha_clase'])->dayOfWeekIso;
            $conflicto = HorarioClase::where('id_docente', $idDocenteSuplente)
                                     ->where('dia_semana', $diaSemana)
                                     ->where('id_bloque', $horario->id_bloque)
                                     ->exists();
            
            if ($conflicto) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', '⚠️ El docente suplente tiene clase asignada en ese horario');
            }
        }
        
        // Crear la suplencia
        $suplencia = Suplencia::create([
            'id_docente_ausente' => $validated['id_docente_ausente'],
            'id_docente_suplente' => $idDocenteSuplente,
            'id_docente_externo' => $idDocenteExterno,
            'id_horario' => $validated['id_horario'],
            'fecha_clase' => $validated['fecha_clase'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);
        
        // Determinar nombre del suplente para el mensaje
        if ($idDocenteExterno) {
            $nombreSuplente = DocenteExterno::find($idDocenteExterno)->nombre_completo . ' (Externo)';
        } else {
            $nombreSuplente = User::find($idDocenteSuplente)->name;
        }
        
        // TODO: Notificar al docente suplente (solo si es interno)
        
        $this->logBitacora($request, [
            'accion' => 'crear_suplencia',
            'modulo' => 'Suplencias',
            'tabla_afectada' => 'suplencias',
            'registro_id' => $suplencia->id_suplencia,
            'descripcion' => "Suplencia asignada: {$nombreSuplente} reemplaza a {$suplencia->docenteAusente->name}",
            'metadata' => [
                'id_docente_ausente' => $suplencia->id_docente_ausente,
                'id_docente_suplente' => $suplencia->id_docente_suplente,
                'id_docente_externo' => $suplencia->id_docente_externo,
                'fecha_clase' => $validated['fecha_clase'],
            ],
            'exitoso' => true
        ]);
        
        return redirect()->route('suplencias.index')
                         ->with('success', '✅ Suplencia registrada correctamente');
    }
    
    /**
     * Buscar docentes disponibles para una suplencia (AJAX)
     */
    public function docentesDisponibles(Request $request)
    {
        $validated = $request->validate([
            'fecha_clase' => 'required|date',
            'id_horario' => 'required|exists:horario_clases,id_horario',
            'id_docente_ausente' => 'required|exists:users,id',
        ]);
        
        $horario = HorarioClase::with('bloque')->findOrFail($validated['id_horario']);
        $diaSemana = Carbon::parse($validated['fecha_clase'])->dayOfWeekIso;
        
        // Docentes que NO tienen clase en ese horario
        $disponibles = User::role(['Docente'])
                          ->where('id', '!=', $validated['id_docente_ausente'])
                          ->whereDoesntHave('horarioClasesComoDocente', function ($query) use ($diaSemana, $horario) {
                              $query->where('dia_semana', $diaSemana)
                                    ->where('id_bloque', $horario->id_bloque);
                          })
                          ->orderBy('name')
                          ->get(['id', 'name', 'email']);
        
        return response()->json([
            'success' => true,
            'docentes' => $disponibles,
            'total' => $disponibles->count()
        ]);
    }

    /**
     * Obtener horarios de un docente (AJAX)
     */
    public function horariosDocente($idDocente)
    {
        try {
            $horarios = HorarioClase::with(['grupo.materia', 'bloque', 'aula'])
                                    ->where('id_docente', $idDocente)
                                    ->orderBy('dia_semana')
                                    ->orderBy('id_bloque')
                                    ->get()
                                    ->map(function ($horario) {
                                        return [
                                            'id_horario' => $horario->id_horario,
                                            'materia' => optional($horario->grupo)->materia->nombre ?? 'Sin materia',
                                            'grupo' => optional($horario->grupo)->nombre_grupo ?? 'Sin grupo',
                                            'bloque' => optional($horario->bloque)->etiqueta ?? 'Sin bloque',
                                            'dia_semana' => $horario->dia_semana,
                                            'aula' => optional($horario->aula)->nombre_aula ?? 'Sin aula',
                                        ];
                                    });

            return response()->json([
                'success' => true,
                'horarios' => $horarios,
                'total' => $horarios->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al obtener horarios del docente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener horarios del docente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eliminar suplencia
     */
    public function destroy(Suplencia $suplencia)
    {
        // Solo se puede eliminar si la fecha aún no pasó
        if (Carbon::parse($suplencia->fecha_clase)->isPast()) {
            return redirect()->back()
                           ->with('error', '❌ No se puede eliminar una suplencia de una fecha pasada');
        }
        
        $idSuplencia = $suplencia->id_suplencia;
        $ausente = $suplencia->docenteAusente->name;
        $suplente = $suplencia->nombre_suplente; // Usa el accessor que maneja internos y externos
        
        $suplencia->delete();
        
        $this->logBitacora(request(), [
            'accion' => 'eliminar_suplencia',
            'modulo' => 'Suplencias',
            'tabla_afectada' => 'suplencias',
            'registro_id' => $idSuplencia,
            'descripcion' => "Suplencia eliminada: {$suplente} reemplazaba a {$ausente}",
            'exitoso' => true
        ]);
        
        return redirect()->route('suplencias.index')
                         ->with('success', '✅ Suplencia eliminada correctamente');
    }
    
    /**
     * Mis suplencias (vista del docente suplente)
     */
    public function misSuplencias()
    {
        $suplencias = Suplencia::with(['docenteAusente', 'horario.grupo.materia', 'horario.aula', 'horario.bloque'])
                               ->where('id_docente_suplente', auth()->id())
                               ->orderBy('fecha_clase', 'desc')
                               ->paginate(15);
        
        return view('suplencias.mis-suplencias', compact('suplencias'));
    }

    /**
     * Obtener lista de docentes externos activos (AJAX)
     */
    public function docentesExternos()
    {
        try {
            $externos = DocenteExterno::activos()
                                     ->orderBy('nombre_completo')
                                     ->get(['id_docente_externo', 'nombre_completo', 'especialidad', 'telefono', 'email']);
            
            return response()->json([
                'success' => true,
                'externos' => $externos,
                'total' => $externos->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al obtener docentes externos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener docentes externos',
                'externos' => [],
                'total' => 0
            ], 500);
        }
    }

    /**
     * Guardar un nuevo docente externo (AJAX)
     */
    public function storeDocenteExterno(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre_completo' => 'required|string|max:255',
                'especialidad' => 'nullable|string|max:100',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'observaciones' => 'nullable|string',
            ]);

            $docente = DocenteExterno::create([
                'nombre_completo' => $validated['nombre_completo'],
                'especialidad' => $validated['especialidad'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'activo' => true,
            ]);

            $this->logBitacora($request, [
                'accion' => 'crear_docente_externo',
                'modulo' => 'Suplencias',
                'tabla_afectada' => 'docente_externos',
                'registro_id' => $docente->id_docente_externo,
                'descripcion' => "Docente externo registrado: {$docente->nombre_completo}",
                'exitoso' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Docente externo guardado correctamente',
                'docente' => $docente
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al guardar docente externo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el docente externo: ' . $e->getMessage()
            ], 500);
        }
    }
}
