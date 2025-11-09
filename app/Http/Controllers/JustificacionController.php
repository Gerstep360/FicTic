<?php

namespace App\Http\Controllers;

use App\Models\Justificacion;
use App\Models\User;
use App\Models\HorarioClase;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JustificacionController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:gestionar_justificaciones|solicitar_justificacion');
    }
    
    /**
     * Listado de justificaciones (Coordinador/Director)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Justificacion::class);
        
        $query = Justificacion::with(['docente', 'resolutor'])
                              ->orderBy('fecha_solicitud', 'desc');
        
        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('id_docente')) {
            $query->where('id_docente', $request->id_docente);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_clase', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_clase', '<=', $request->fecha_hasta);
        }
        
        $justificaciones = $query->paginate(20);
        
        // Lista de docentes para filtro
        $docentes = User::role(['Docente'])
                       ->orderBy('name')
                       ->get(['id', 'name']);
        
        return view('justificaciones.index', compact('justificaciones', 'docentes'));
    }
    
    /**
     * Mostrar detalle de una justificación
     */
    public function show(Justificacion $justificacion)
    {
        $this->authorize('view', $justificacion);
        
        $justificacion->load(['docente', 'resolutor']);
        
        return view('justificaciones.show', compact('justificacion'));
    }
    
    /**
     * Formulario para crear justificación (Docente)
     */
    public function create()
    {
        $this->authorize('create', Justificacion::class);
        
        // Obtener horarios del docente autenticado
        $horarios = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                ->where('id_docente', auth()->id())
                                ->get();
        
        return view('justificaciones.create', compact('horarios'));
    }
    
    /**
     * Guardar nueva justificación
     */
    public function store(Request $request)
    {
        $this->authorize('create', Justificacion::class);
        
        $validated = $request->validate([
            'fecha_clase' => 'required|date',
            'id_horario' => 'nullable|exists:horario_clases,id_horario',
            'motivo' => 'required|string|min:10|max:1000',
            'documento_adjunto' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        $justificacion = Justificacion::create([
            'id_docente' => auth()->id(),
            'fecha_clase' => $validated['fecha_clase'],
            'motivo' => $validated['motivo'],
            'estado' => 'PENDIENTE',
            'fecha_solicitud' => now(),
        ]);
        
        // TODO: Guardar archivo adjunto si existe
        // TODO: Notificar al coordinador/director
        
        $this->logBitacora($request, [
            'accion' => 'crear_justificacion',
            'modulo' => 'Justificaciones',
            'tabla_afectada' => 'justificaciones',
            'registro_id' => $justificacion->id_justif,
            'descripcion' => "Solicitud de justificación creada por {$justificacion->docente->name} para el {$validated['fecha_clase']}",
            'metadata' => [
                'id_docente' => $justificacion->id_docente,
                'fecha_clase' => $validated['fecha_clase'],
            ],
            'exitoso' => true
        ]);
        
        return redirect()->route('justificaciones.index')
                         ->with('success', '✅ Solicitud de justificación enviada correctamente');
    }
    
    /**
     * Aprobar justificación
     */
    public function aprobar(Request $request, Justificacion $justificacion)
    {
        $this->authorize('resolver', $justificacion);
        
        if ($justificacion->estado !== 'PENDIENTE') {
            return redirect()->back()->with('error', '❌ Esta justificación ya fue resuelta');
        }
        
        $justificacion->update([
            'estado' => 'APROBADA',
            'fecha_resolucion' => now(),
            'resuelta_por' => auth()->id(),
        ]);
        
        $this->logBitacora($request, [
            'accion' => 'aprobar_justificacion',
            'modulo' => 'Justificaciones',
            'tabla_afectada' => 'justificaciones',
            'registro_id' => $justificacion->id_justificacion,
            'descripcion' => "Justificación APROBADA por " . auth()->user()->name,
            'cambios_antes' => ['estado' => 'PENDIENTE'],
            'cambios_despues' => ['estado' => 'APROBADA'],
            'exitoso' => true
        ]);
        
        return redirect()->route('justificaciones.show', $justificacion)
                         ->with('success', '✅ Justificación aprobada correctamente');
    }
    
    /**
     * Rechazar justificación
     */
    public function rechazar(Request $request, Justificacion $justificacion)
    {
        $this->authorize('resolver', $justificacion);
        
        if ($justificacion->estado !== 'PENDIENTE') {
            return redirect()->back()->with('error', '❌ Esta justificación ya fue resuelta');
        }
        
        $validated = $request->validate([
            'observaciones' => 'required|string|min:10|max:500',
        ]);
        
        $justificacion->update([
            'estado' => 'RECHAZADA',
            'fecha_resolucion' => now(),
            'resuelta_por' => auth()->id(),
            'observaciones' => $validated['observaciones'],
        ]);
        
        $this->logBitacora($request, [
            'accion' => 'rechazar_justificacion',
            'modulo' => 'Justificaciones',
            'tabla_afectada' => 'justificaciones',
            'registro_id' => $justificacion->id_justificacion,
            'descripcion' => "Justificación RECHAZADA por " . auth()->user()->name,
            'cambios_antes' => ['estado' => 'PENDIENTE'],
            'cambios_despues' => ['estado' => 'RECHAZADA', 'observaciones' => $validated['observaciones']],
            'exitoso' => true
        ]);
        
        return redirect()->route('justificaciones.show', $justificacion)
                         ->with('success', '❌ Justificación rechazada');
    }
    
    /**
     * Aprobar/Rechazar justificación (Coordinador/Director)
     */
    public function resolver(Request $request, Justificacion $justificacion)
    {
        $this->authorize('resolver', $justificacion);
        
        $validated = $request->validate([
            'decision' => 'required|in:APROBADA,RECHAZADA',
            'observaciones' => 'nullable|string|max:500',
        ]);
        
        $estadoAnterior = $justificacion->estado;
        
        $justificacion->update([
            'estado' => $validated['decision'],
            'fecha_resolucion' => now(),
            'resuelta_por' => auth()->id(),
            'observaciones' => $validated['observaciones'] ?? null,
        ]);
        
        // TODO: Notificar al docente de la resolución
        
        $this->logBitacora($request, [
            'accion' => 'resolver_justificacion',
            'modulo' => 'Justificaciones',
            'tabla_afectada' => 'justificaciones',
            'registro_id' => $justificacion->id_justif,
            'descripcion' => "Justificación {$validated['decision']} por " . auth()->user()->name,
            'metadata' => [
                'id_docente' => $justificacion->id_docente,
                'decision' => $validated['decision'],
                'estado_anterior' => $estadoAnterior,
            ],
            'cambios_antes' => ['estado' => $estadoAnterior],
            'cambios_despues' => ['estado' => $validated['decision']],
            'exitoso' => true
        ]);
        
        $mensaje = $validated['decision'] === 'APROBADA' 
            ? '✅ Justificación aprobada correctamente'
            : '❌ Justificación rechazada';
        
        return redirect()->route('justificaciones.show', $justificacion)
                         ->with('success', $mensaje);
    }
    
    /**
     * Mis justificaciones (vista del docente)
     */
    public function misJustificaciones()
    {
        $justificaciones = Justificacion::with(['resolutor'])
                                        ->where('id_docente', auth()->id())
                                        ->orderBy('fecha_solicitud', 'desc')
                                        ->paginate(15);
        
        return view('justificaciones.mis-justificaciones', compact('justificaciones'));
    }
    
    /**
     * Eliminar justificación (solo pendientes)
     */
    public function destroy(Justificacion $justificacion)
    {
        $this->authorize('delete', $justificacion);
        
        if ($justificacion->estado !== 'PENDIENTE') {
            return redirect()->back()
                           ->with('error', '❌ No se puede eliminar una justificación ya resuelta');
        }
        
        $idJustif = $justificacion->id_justif;
        $docente = $justificacion->docente->name;
        
        $justificacion->delete();
        
        $this->logBitacora(request(), [
            'accion' => 'eliminar_justificacion',
            'modulo' => 'Justificaciones',
            'tabla_afectada' => 'justificaciones',
            'registro_id' => $idJustif,
            'descripcion' => "Justificación eliminada (docente: {$docente})",
            'exitoso' => true
        ]);
        
        return redirect()->route('justificaciones.index')
                         ->with('success', '✅ Justificación eliminada correctamente');
    }
}
