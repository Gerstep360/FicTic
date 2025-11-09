<?php

namespace App\Http\Controllers;

use App\Models\Suplencia;
use App\Models\Justificacion;
use App\Models\User;
use App\Models\HorarioClase;
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
        $query = Suplencia::with(['docenteAusente', 'docenteSuplente', 'horario.grupo.materia', 'horario.aula'])
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
        $suplencia->load(['docenteAusente', 'docenteSuplente', 'horario.grupo.materia', 'horario.aula', 'horario.bloque']);
        
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
        
        // Horarios del docente ausente (si viene de justificación)
        $horarios = [];
        if ($justificacion) {
            $horarios = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                    ->where('id_docente', $justificacion->id_docente)
                                    ->get();
        }
        
        // Docentes disponibles para suplir
        $docentes = User::role(['Docente'])
                       ->where('id', '!=', $justificacion->id_docente ?? 0)
                       ->orderBy('name')
                       ->get(['id', 'name']);
        
        return view('suplencias.create', compact('justificacion', 'horarios', 'docentes'));
    }
    
    /**
     * Guardar nueva suplencia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_docente_ausente' => 'required|exists:users,id',
            'id_docente_suplente' => 'required|exists:users,id|different:id_docente_ausente',
            'id_horario' => 'required|exists:horario_clases,id_horario',
            'fecha_clase' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ]);
        
        // Verificar que el horario pertenece al docente ausente
        $horario = HorarioClase::where('id_horario', $validated['id_horario'])
                               ->where('id_docente', $validated['id_docente_ausente'])
                               ->firstOrFail();
        
        // Verificar que no exista ya una suplencia para ese horario y fecha
        $existe = Suplencia::where('id_horario', $validated['id_horario'])
                          ->where('fecha_clase', $validated['fecha_clase'])
                          ->exists();
        
        if ($existe) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', '❌ Ya existe una suplencia registrada para ese horario y fecha');
        }
        
        // Verificar disponibilidad del suplente (que no tenga clase a esa hora)
        $diaSemana = Carbon::parse($validated['fecha_clase'])->dayOfWeekIso;
        $conflicto = HorarioClase::where('id_docente', $validated['id_docente_suplente'])
                                 ->where('dia_semana', $diaSemana)
                                 ->where('id_bloque', $horario->id_bloque)
                                 ->exists();
        
        if ($conflicto) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', '⚠️ El docente suplente tiene clase asignada en ese horario');
        }
        
        $suplencia = Suplencia::create([
            'id_docente_ausente' => $validated['id_docente_ausente'],
            'id_docente_suplente' => $validated['id_docente_suplente'],
            'id_horario' => $validated['id_horario'],
            'fecha_clase' => $validated['fecha_clase'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);
        
        // TODO: Notificar al docente suplente
        
        $this->logBitacora($request, [
            'accion' => 'crear_suplencia',
            'modulo' => 'Suplencias',
            'tabla_afectada' => 'suplencias',
            'registro_id' => $suplencia->id_suplencia,
            'descripcion' => "Suplencia asignada: {$suplencia->docenteSuplente->name} reemplaza a {$suplencia->docenteAusente->name}",
            'metadata' => [
                'id_docente_ausente' => $suplencia->id_docente_ausente,
                'id_docente_suplente' => $suplencia->id_docente_suplente,
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
        $suplente = $suplencia->docenteSuplente->name;
        
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
}
