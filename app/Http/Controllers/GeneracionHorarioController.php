<?php

namespace App\Http\Controllers;

use App\Models\GeneracionHorario;
use App\Models\Gestion;
use App\Models\Carrera;
use App\Models\Grupo;
use App\Models\HorarioClase;
use App\Models\Bloque;
use App\Services\OptimizadorHorarios;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneracionHorarioController extends Controller
{
    use LogsBitacora;

    protected $dias = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:generar_horario_auto|Admin DTIC'])
            ->except(['index', 'show']);
    }

    /**
     * Listado de generaciones de horarios
     */
    public function index(Request $request)
    {
        $query = GeneracionHorario::with(['gestion', 'carrera', 'usuario'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('id_gestion')) {
            $query->where('id_gestion', $request->id_gestion);
        }

        if ($request->filled('id_carrera')) {
            $query->where('id_carrera', $request->id_carrera);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $generaciones = $query->paginate(15)->appends($request->query());
        $gestiones = Gestion::orderBy('nombre', 'desc')->get();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('generacion-horarios.index', compact('generaciones', 'gestiones', 'carreras'));
    }

    /**
     * Formulario para nueva generación
     */
    public function create()
    {
        $gestiones = Gestion::orderBy('nombre', 'desc')->get();
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();

        return view('generacion-horarios.create', compact('gestiones', 'carreras'));
    }

    /**
     * Ejecutar generación automática
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_gestion' => ['required', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['nullable', 'exists:carreras,id_carrera'],
            'minimizar_huecos' => ['boolean'],
            'balancear_carga_diaria' => ['boolean'],
            'respetar_preferencias' => ['boolean'],
            'max_horas_dia_docente' => ['integer', 'min:1', 'max:8'],
            'intentos_por_grupo' => ['integer', 'min:10', 'max:500'],
        ]);

        // Configuración de optimización
        $configuracion = [
            'minimizar_huecos' => $request->boolean('minimizar_huecos', true),
            'balancear_carga_diaria' => $request->boolean('balancear_carga_diaria', true),
            'respetar_preferencias' => $request->boolean('respetar_preferencias', true),
            'max_horas_dia_docente' => $request->input('max_horas_dia_docente', 4),
            'intentos_por_grupo' => $request->input('intentos_por_grupo', 100),
        ];

        // Crear registro de generación
        $generacion = GeneracionHorario::create([
            'id_gestion' => $validated['id_gestion'],
            'id_carrera' => $validated['id_carrera'] ?? null,
            'id_usuario' => auth()->id(),
            'configuracion' => $configuracion,
            'estado' => 'pendiente',
        ]);

        try {
            // Marcar como procesando
            $generacion->marcarComoProcesando();

            // Ejecutar optimizador
            $optimizador = new OptimizadorHorarios(
                $validated['id_gestion'],
                $validated['id_carrera'] ?? null,
                $configuracion
            );

            $resultado = $optimizador->generar();

            if ($resultado['success']) {
                $generacion->marcarComoCompletado(
                    $resultado['asignaciones'],
                    $resultado['metricas']
                );

                // Registrar en bitácora
                $this->logBitacora($request, [
                    'accion' => 'generar',
                    'modulo' => 'Generación Automática de Horarios',
                    'tabla_afectada' => 'generacion_horarios',
                    'registro_id' => $generacion->id_generacion,
                    'descripcion' => "Generación automática completada para " . 
                        ($generacion->carrera ? $generacion->carrera->nombre_carrera : 'toda la facultad') . 
                        " - {$resultado['metricas']['grupos_asignados']}/{$resultado['metricas']['total_grupos']} grupos asignados",
                    'id_gestion' => $validated['id_gestion'],
                    'exitoso' => true,
                ]);

                return redirect()
                    ->route('generacion-horarios.show', $generacion)
                    ->with('success', 'Generación completada exitosamente. ' . $resultado['mensaje']);
            } else {
                $generacion->marcarComoError($resultado['mensaje']);

                $this->logBitacora($request, [
                    'accion' => 'generar',
                    'modulo' => 'Generación Automática de Horarios',
                    'tabla_afectada' => 'generacion_horarios',
                    'registro_id' => $generacion->id_generacion,
                    'descripcion' => "Error en generación automática: " . $resultado['mensaje'],
                    'id_gestion' => $validated['id_gestion'],
                    'exitoso' => false,
                ]);

                return redirect()
                    ->route('generacion-horarios.show', $generacion)
                    ->with('error', 'La generación finalizó con errores. ' . $resultado['mensaje']);
            }
        } catch (\Exception $e) {
            $generacion->marcarComoError($e->getMessage());

            $this->logBitacora($request, [
                'accion' => 'generar',
                'modulo' => 'Generación Automática de Horarios',
                'tabla_afectada' => 'generacion_horarios',
                'registro_id' => $generacion->id_generacion,
                'descripcion' => "Error en generación: " . $e->getMessage(),
                'id_gestion' => $validated['id_gestion'],
                'exitoso' => false,
            ]);

            return redirect()
                ->route('generacion-horarios.index')
                ->with('error', 'Error al generar horarios: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de una generación con preview del horario
     */
    public function show(GeneracionHorario $generacionHorario)
    {
        $generacionHorario->load(['gestion', 'carrera', 'usuario']);
        
        $bloques = Bloque::orderBy('hora_inicio')->get();
        $matriz = [];

        // Si hay resultados, construir matriz de visualización
        if ($generacionHorario->resultado && !empty($generacionHorario->resultado)) {
            $matriz = $this->construirMatrizHorarios($generacionHorario, $bloques);
        }

        return view('generacion-horarios.show', compact('generacionHorario', 'matriz', 'bloques'));
    }

    /**
     * Aplicar horarios generados a la base de datos
     */
    public function aplicar(Request $request, GeneracionHorario $generacionHorario)
    {
        if (!$generacionHorario->puede_aplicarse) {
            return redirect()
                ->back()
                ->with('error', 'Esta generación no puede aplicarse.');
        }

        try {
            DB::beginTransaction();

            // Eliminar horarios existentes para esta gestión/carrera
            $query = HorarioClase::whereHas('grupo', function($q) use ($generacionHorario) {
                $q->where('id_gestion', $generacionHorario->id_gestion);
                
                if ($generacionHorario->id_carrera) {
                    $q->whereHas('materia', function($qq) use ($generacionHorario) {
                        $qq->where('id_carrera', $generacionHorario->id_carrera);
                    });
                }
            });

            $horariosEliminados = $query->count();
            $query->delete();

            // Insertar nuevos horarios
            foreach ($generacionHorario->resultado as $asignacion) {
                HorarioClase::create([
                    'dia_semana' => $asignacion['dia_semana'],
                    'id_bloque' => $asignacion['id_bloque'],
                    'id_aula' => $asignacion['id_aula'],
                    'id_grupo' => $asignacion['id_grupo'],
                    'id_docente' => $asignacion['id_docente'],
                ]);
            }

            // Marcar generación como aplicada
            $generacionHorario->marcarComoAplicado();

            DB::commit();

            // Registrar en bitácora
            $this->logBitacora($request, [
                'accion' => 'aplicar',
                'modulo' => 'Generación Automática de Horarios',
                'tabla_afectada' => 'horario_clases',
                'registro_id' => $generacionHorario->id_generacion,
                'descripcion' => "Se aplicaron {$generacionHorario->grupos_asignados} horarios generados automáticamente. " .
                    "Horarios anteriores eliminados: {$horariosEliminados}",
                'id_gestion' => $generacionHorario->id_gestion,
                'exitoso' => true,
            ]);

            return redirect()
                ->route('generacion-horarios.show', $generacionHorario)
                ->with('success', "Horarios aplicados exitosamente. Se crearon {$generacionHorario->grupos_asignados} asignaciones.");
        } catch (\Exception $e) {
            DB::rollBack();

            $this->logBitacora($request, [
                'accion' => 'aplicar',
                'modulo' => 'Generación Automática de Horarios',
                'tabla_afectada' => 'horario_clases',
                'registro_id' => $generacionHorario->id_generacion,
                'descripcion' => "Error al aplicar horarios: " . $e->getMessage(),
                'id_gestion' => $generacionHorario->id_gestion,
                'exitoso' => false,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al aplicar horarios: ' . $e->getMessage());
        }
    }

    /**
     * Descargar PDF del horario generado
     */
    public function pdf(GeneracionHorario $generacionHorario)
    {
        $generacionHorario->load(['gestion', 'carrera', 'usuario']);
        $bloques = Bloque::orderBy('hora_inicio')->get();
        $matriz = $this->construirMatrizHorarios($generacionHorario, $bloques);

        // Registrar en bitácora
        $this->logBitacora(request(), [
            'accion' => 'exportar_pdf',
            'modulo' => 'Generación Automática de Horarios',
            'tabla_afectada' => 'generacion_horarios',
            'registro_id' => $generacionHorario->id_generacion,
            'descripcion' => "Descarga de PDF de horario generado #{$generacionHorario->id_generacion}",
            'id_gestion' => $generacionHorario->id_gestion,
            'exitoso' => true,
        ]);

        $pdf = Pdf::loadView('generacion-horarios.pdf', [
            'generacionHorario' => $generacionHorario,
            'matriz' => $matriz,
            'bloques' => $bloques,
            'dias' => $this->dias,
        ]);

        $filename = 'horario_' . 
            ($generacionHorario->carrera ? $generacionHorario->carrera->nombre_carrera : 'facultad') . 
            '_' . $generacionHorario->gestion->nombre . 
            '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Eliminar una generación
     */
    public function destroy(Request $request, GeneracionHorario $generacionHorario)
    {
        if ($generacionHorario->estado === 'aplicado') {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar una generación que ya ha sido aplicada.');
        }

        $id = $generacionHorario->id_generacion;
        $generacionHorario->delete();

        $this->logBitacora($request, [
            'accion' => 'eliminar',
            'modulo' => 'Generación Automática de Horarios',
            'tabla_afectada' => 'generacion_horarios',
            'registro_id' => $id,
            'descripcion' => "Generación de horario #{$id} eliminada",
            'id_gestion' => $generacionHorario->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->route('generacion-horarios.index')
            ->with('success', 'Generación eliminada exitosamente.');
    }

    /**
     * Construir matriz de horarios para visualización
     */
    protected function construirMatrizHorarios($generacion, $bloques)
    {
        $matriz = [];
        
        // Cargar información completa de cada asignación
        foreach ($generacion->resultado as $asignacion) {
            $grupo = Grupo::with(['materia', 'docente'])->find($asignacion['id_grupo']);
            $dia = $asignacion['dia_semana'];
            $bloque = $asignacion['id_bloque'];
            
            if (!isset($matriz[$dia])) {
                $matriz[$dia] = [
                    'nombre' => $this->dias[$dia],
                    'bloques' => [],
                ];
            }
            
            if (!isset($matriz[$dia]['bloques'][$bloque])) {
                $matriz[$dia]['bloques'][$bloque] = [];
            }
            
            $matriz[$dia]['bloques'][$bloque][] = [
                'grupo' => $grupo,
                'aula_codigo' => $asignacion['id_aula'],
                'materia' => $grupo->materia->nombre ?? 'N/A',
                'nombre_grupo' => $grupo->nombre_grupo ?? 'N/A',
                'docente' => $grupo->docente->name ?? 'Sin docente',
            ];
        }
        
        return $matriz;
    }
}
