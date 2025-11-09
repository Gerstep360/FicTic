<?php

namespace App\Http\Controllers;

use App\Models\AprobacionHorario;
use App\Models\HorarioClase;
use App\Models\Gestion;
use App\Models\Carrera;
use App\Services\ValidadorHorarios;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AprobacionHorarioController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        
        // Coordinador puede crear y enviar
        $this->middleware(['permission:asignar_horarios|Admin DTIC'])
            ->only(['index', 'show', 'create', 'store', 'enviarDirector', 'responderObservaciones']);
        
        // Director aprueba/observa
        $this->middleware(['permission:aprobar_horarios|Admin DTIC'])
            ->only(['pendientesDirector', 'aprobarDirector', 'observarDirector', 'enviarDecano']);
        
        // Decano aprueba final
        $this->middleware(['permission:publicar_horarios|Admin DTIC'])
            ->only(['pendientesDecano', 'aprobarDecano', 'observarDecano']);
    }

    // ==================== Coordinador ====================

    /**
     * Lista de aprobaciones (vista del coordinador)
     */
    public function index(Request $request)
    {
        $query = AprobacionHorario::with(['gestion', 'carrera', 'coordinador', 'director', 'decano']);

        if ($request->filled('id_gestion')) {
            $query->deGestion($request->id_gestion);
        }

        if ($request->filled('id_carrera')) {
            $query->deCarrera($request->id_carrera);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $aprobaciones = $query->orderBy('updated_at', 'desc')->paginate(15);
        
        $gestiones = Gestion::orderBy('nombre', 'desc')->get();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('aprobaciones.index', compact('aprobaciones', 'gestiones', 'carreras'));
    }

    /**
     * Detalle de una aprobación
     */
    public function show(AprobacionHorario $aprobacion)
    {
        $aprobacion->load(['gestion', 'carrera', 'coordinador', 'director', 'decano']);
        
        // Obtener horarios de esta aprobación (a través de la relación con grupos)
        $horarios = HorarioClase::with(['grupo.materia', 'docente', 'aula', 'bloque'])
            ->whereHas('grupo', function($q) use ($aprobacion) {
                $q->where('id_gestion', $aprobacion->id_gestion);
                
                if ($aprobacion->id_carrera) {
                    $q->whereHas('materia', function($qq) use ($aprobacion) {
                        $qq->where('id_carrera', $aprobacion->id_carrera);
                    });
                }
            })
            ->orderBy('dia_semana')
            ->get();

        return view('aprobaciones.show', compact('aprobacion', 'horarios'));
    }

    /**
     * Crear nueva aprobación
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_gestion' => ['required', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['nullable', 'exists:carreras,id_carrera'],
        ]);

        // Verificar que no exista ya una aprobación para esta gestión-carrera
        $existe = AprobacionHorario::where('id_gestion', $validated['id_gestion'])
            ->where('id_carrera', $validated['id_carrera'])
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->with('error', 'Ya existe un proceso de aprobación para esta gestión y carrera.');
        }

        // Contar horarios actuales (a través de la relación con grupos)
        $query = HorarioClase::whereHas('grupo', function($q) use ($validated) {
            $q->where('id_gestion', $validated['id_gestion']);
            
            if (isset($validated['id_carrera']) && $validated['id_carrera']) {
                $q->whereHas('materia', function($qq) use ($validated) {
                    $qq->where('id_carrera', $validated['id_carrera']);
                });
            }
        });
        
        $totalHorarios = $query->count();

        // Ejecutar validación para obtener conflictos
        $validador = new ValidadorHorarios(
            $validated['id_gestion'],
            $validated['id_carrera'] ?? null
        );
        $resultado = $validador->validar();

        $aprobacion = AprobacionHorario::create([
            'id_gestion' => $validated['id_gestion'],
            'id_carrera' => $validated['id_carrera'],
            'estado' => 'borrador',
            'total_horarios' => $totalHorarios,
            'horarios_validados' => $resultado['success'] ? $totalHorarios : ($totalHorarios - $resultado['resumen']['total_conflictos']),
            'conflictos_pendientes' => $resultado['resumen']['total_conflictos'],
            'metadata' => [
                'validacion_inicial' => $resultado['resumen'],
            ],
        ]);

        $this->logBitacora($request, [
            'accion' => 'crear',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Proceso de aprobación iniciado para {$aprobacion->alcance_texto} - Gestión {$aprobacion->gestion->nombre}",
            'id_gestion' => $validated['id_gestion'],
            'exitoso' => true,
        ]);

        return redirect()
            ->route('aprobaciones.show', $aprobacion->id_aprobacion)
            ->with('success', 'Proceso de aprobación creado exitosamente.');
    }

    /**
     * Enviar al Director para aprobación
     */
    public function enviarDirector(Request $request, AprobacionHorario $aprobacion)
    {
        if (!$aprobacion->puede_enviar_director) {
            return redirect()
                ->back()
                ->with('error', 'El horario no puede ser enviado en su estado actual.');
        }

        if ($aprobacion->conflictos_pendientes > 0) {
            return redirect()
                ->back()
                ->with('warning', 'Existen conflictos pendientes. Se recomienda resolverlos antes de enviar.');
        }

        $aprobacion->enviarADirector(auth()->id());

        $this->logBitacora($request, [
            'accion' => 'enviar_aprobacion',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Horario enviado al Director para aprobación: {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Horario enviado al Director para su revisión.');
    }

    /**
     * Coordinador responde a observaciones
     */
    public function responderObservaciones(Request $request, AprobacionHorario $aprobacion)
    {
        $validated = $request->validate([
            'respuesta' => ['required', 'string', 'max:1000'],
        ]);

        if (!in_array($aprobacion->estado, ['observado_director', 'observado_decano'])) {
            return redirect()
                ->back()
                ->with('error', 'No hay observaciones pendientes de responder.');
        }

        $aprobacion->responderObservaciones($validated['respuesta']);

        $this->logBitacora($request, [
            'accion' => 'responder_observaciones',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Coordinador respondió observaciones: {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Respuesta registrada. El horario vuelve a estado de elaboración.');
    }

    // ==================== Director ====================

    /**
     * Lista de horarios pendientes de aprobación del Director
     */
    public function pendientesDirector(Request $request)
    {
        $query = AprobacionHorario::with(['gestion', 'carrera', 'coordinador'])
            ->pendientesDirector();

        $aprobaciones = $query->orderBy('fecha_envio_director', 'asc')->paginate(15);

        return view('aprobaciones.pendientes-director', compact('aprobaciones'));
    }

    /**
     * Director aprueba el horario
     */
    public function aprobarDirector(Request $request, AprobacionHorario $aprobacion)
    {
        if (!$aprobacion->puede_aprobar_director) {
            return redirect()
                ->back()
                ->with('error', 'Este horario no puede ser aprobado en su estado actual.');
        }

        $validated = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $aprobacion->aprobarDirector(auth()->id(), $validated['observaciones'] ?? null);

        $this->logBitacora($request, [
            'accion' => 'aprobar_director',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Director aprobó horario: {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->route('aprobaciones.pendientes-director')
            ->with('success', 'Horario aprobado exitosamente.');
    }

    /**
     * Director observa y solicita cambios
     */
    public function observarDirector(Request $request, AprobacionHorario $aprobacion)
    {
        if (!$aprobacion->puede_aprobar_director) {
            return redirect()
                ->back()
                ->with('error', 'Este horario no puede ser observado en su estado actual.');
        }

        $validated = $request->validate([
            'observaciones' => ['required', 'string', 'max:1000'],
        ]);

        $aprobacion->observarDirector(auth()->id(), $validated['observaciones']);

        $this->logBitacora($request, [
            'accion' => 'observar_director',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Director observó horario con solicitud de cambios: {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->route('aprobaciones.pendientes-director')
            ->with('success', 'Observaciones enviadas al coordinador.');
    }

    /**
     * Director envía al Decano (consolidado)
     */
    public function enviarDecano(Request $request, AprobacionHorario $aprobacion)
    {
        if (!$aprobacion->puede_enviar_decano) {
            return redirect()
                ->back()
                ->with('error', 'El horario no puede ser enviado al Decano en su estado actual.');
        }

        $aprobacion->enviarADecano();

        $this->logBitacora($request, [
            'accion' => 'enviar_decano',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Horario enviado al Decano para aprobación final: {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Horario enviado al Decano para aprobación final.');
    }

    // ==================== Decano ====================

    /**
     * Lista de horarios pendientes de aprobación del Decano
     */
    public function pendientesDecano(Request $request)
    {
        $query = AprobacionHorario::with(['gestion', 'carrera', 'coordinador', 'director'])
            ->pendientesDecano();

        $aprobaciones = $query->orderBy('fecha_envio_decano', 'asc')->paginate(15);

        return view('aprobaciones.pendientes-decano', compact('aprobaciones'));
    }

    /**
     * Decano aprueba (aprobación final)
     */
    public function aprobarDecano(Request $request, AprobacionHorario $aprobacion)
    {
        if (!$aprobacion->puede_aprobar_decano) {
            return redirect()
                ->back()
                ->with('error', 'Este horario no puede ser aprobado en su estado actual.');
        }

        $validated = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $aprobacion->aprobarDecano(auth()->id(), $validated['observaciones'] ?? null);

        $this->logBitacora($request, [
            'accion' => 'aprobar_decano',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Decano aprobó horario (aprobación final): {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->route('aprobaciones.pendientes-decano')
            ->with('success', 'Horario aprobado. Listo para publicación.');
    }

    /**
     * Decano observa y solicita cambios
     */
    public function observarDecano(Request $request, AprobacionHorario $aprobacion)
    {
        if (!$aprobacion->puede_aprobar_decano) {
            return redirect()
                ->back()
                ->with('error', 'Este horario no puede ser observado en su estado actual.');
        }

        $validated = $request->validate([
            'observaciones' => ['required', 'string', 'max:1000'],
        ]);

        $aprobacion->observarDecano(auth()->id(), $validated['observaciones']);

        $this->logBitacora($request, [
            'accion' => 'observar_decano',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $aprobacion->id_aprobacion,
            'descripcion' => "Decano observó horario con solicitud de cambios: {$aprobacion->alcance_texto}",
            'id_gestion' => $aprobacion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->route('aprobaciones.pendientes-decano')
            ->with('success', 'Observaciones enviadas.');
    }

    /**
     * Eliminar aprobación (solo en borrador)
     */
    public function destroy(Request $request, AprobacionHorario $aprobacion)
    {
        if ($aprobacion->estado !== 'borrador') {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden eliminar aprobaciones en estado borrador.');
        }

        $id = $aprobacion->id_aprobacion;
        $alcance = $aprobacion->alcance_texto;
        
        $aprobacion->delete();

        $this->logBitacora($request, [
            'accion' => 'eliminar',
            'modulo' => 'Aprobación de Horarios',
            'tabla_afectada' => 'aprobaciones_horario',
            'registro_id' => $id,
            'descripcion' => "Proceso de aprobación eliminado: {$alcance}",
            'exitoso' => true,
        ]);

        return redirect()
            ->route('aprobaciones.index')
            ->with('success', 'Proceso de aprobación eliminado.');
    }
}
