<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use App\Models\HorarioClase;
use App\Models\Grupo;
use App\Models\User;
use App\Models\Aula;
use App\Models\Carrera;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicacionHorarioController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        
        // Solo Decano y Admin DTIC pueden publicar
        $this->middleware(['permission:publicar_horarios|Admin DTIC'])
            ->only(['index', 'publicar', 'despublicar']);
        
        // Las vistas públicas no requieren autenticación (se maneja en las rutas)
    }

    /**
     * Panel de publicación (Decano/Admin)
     */
    public function index()
    {
        $gestiones = Gestion::with([
            'aprobaciones.carrera.facultad',
            'aprobaciones.coordinador',
            'aprobaciones.director',
            'aprobaciones.decano',
            'usuarioPublicador'
        ])
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return view('publicacion.index', compact('gestiones'));
    }

    /**
     * Vista previa detallada antes de publicar
     */
    public function preview(Gestion $gestion)
    {
        // Cargar todas las aprobaciones con sus relaciones
        $gestion->load([
            'aprobaciones.carrera.facultad',
            'aprobaciones.coordinador',
            'aprobaciones.director',
            'aprobaciones.decano'
        ]);

        // Obtener TODOS los horarios de esta gestión con todas las relaciones
        $horarios = \App\Models\HorarioClase::with([
            'grupo.materia.carrera.facultad',
            'docente',
            'aula',
            'bloque'
        ])
        ->whereHas('grupo', function($q) use ($gestion) {
            $q->where('id_gestion', $gestion->id_gestion);
        })
        ->orderBy('dia_semana')
        ->orderBy('id_bloque')
        ->get();

        // Agrupar por carrera para mejor organización
        $horariosPorCarrera = $horarios->groupBy(function($horario) {
            return $horario->grupo->materia->carrera->nombre_carrera ?? 'Sin Carrera';
        });

        // Estadísticas generales
        $stats = [
            'total_horarios' => $horarios->count(),
            'total_docentes' => $horarios->pluck('id_docente')->unique()->count(),
            'total_aulas' => $horarios->pluck('id_aula')->unique()->count(),
            'total_materias' => $horarios->pluck('grupo.materia.id_materia')->unique()->count(),
            'total_grupos' => $horarios->pluck('id_grupo')->unique()->count(),
        ];

        // Docentes únicos con su carga horaria
        $docentes = $horarios->groupBy('id_docente')->map(function($horariosDocente) {
            $docente = $horariosDocente->first()->docente;
            return [
                'docente' => $docente,
                'total_horas' => $horariosDocente->count() * 2, // Asumiendo bloques de 2 horas
                'materias' => $horariosDocente->pluck('grupo.materia.nombre')->unique()->values(),
            ];
        })->sortByDesc('total_horas');

        // Aulas y su ocupación
        $aulas = $horarios->groupBy('id_aula')->map(function($horariosAula) {
            $aula = $horariosAula->first()->aula;
            return [
                'aula' => $aula,
                'ocupacion' => $horariosAula->count(),
                'grupos' => $horariosAula->pluck('grupo.nombre')->unique()->count(),
            ];
        })->sortByDesc('ocupacion');

        return view('publicacion.preview', compact(
            'gestion',
            'horarios',
            'horariosPorCarrera',
            'stats',
            'docentes',
            'aulas'
        ));
    }

    /**
     * Publicar una gestión completa
     */
    public function publicar(Request $request, Gestion $gestion)
    {
        if ($gestion->publicada) {
            return redirect()
                ->back()
                ->with('warning', 'Esta gestión ya está publicada.');
        }

        if (!$gestion->puede_publicar) {
            return redirect()
                ->back()
                ->with('error', 'No se puede publicar. Existen aprobaciones pendientes.');
        }

        $validated = $request->validate([
            'nota' => ['nullable', 'string', 'max:500'],
        ]);

        $gestion->publicar(auth()->id(), $validated['nota'] ?? null);

        $this->logBitacora($request, [
            'accion' => 'publicar',
            'modulo' => 'Publicación de Horarios',
            'tabla_afectada' => 'gestiones',
            'registro_id' => $gestion->id_gestion,
            'descripcion' => "Gestión publicada: {$gestion->nombre}",
            'id_gestion' => $gestion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Horarios publicados exitosamente.');
    }

    /**
     * Despublicar una gestión
     */
    public function despublicar(Request $request, Gestion $gestion)
    {
        if (!$gestion->publicada) {
            return redirect()
                ->back()
                ->with('warning', 'Esta gestión no está publicada.');
        }

        $gestion->despublicar();

        $this->logBitacora($request, [
            'accion' => 'despublicar',
            'modulo' => 'Publicación de Horarios',
            'tabla_afectada' => 'gestiones',
            'registro_id' => $gestion->id_gestion,
            'descripcion' => "Gestión despublicada: {$gestion->nombre}",
            'id_gestion' => $gestion->id_gestion,
            'exitoso' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Publicación revertida exitosamente.');
    }

    /**
     * Vista pública: Horarios por docente
     */
    public function porDocente(Request $request, $token = null)
    {
        $gestiones = Gestion::publicadas()->orderBy('fecha_inicio', 'desc')->get();
        
        $gestionSeleccionada = null;
        $docente = null;
        $horarios = collect();
        $matriz = [];

        if ($request->filled('id_gestion')) {
            $gestionSeleccionada = Gestion::publicadas()->find($request->id_gestion);
        }

        if ($request->filled('id_docente') && $gestionSeleccionada) {
            $docente = User::find($request->id_docente);
            
            if ($docente) {
                $horarios = HorarioClase::with(['grupo.materia.carrera', 'aula', 'bloque'])
                    ->whereHas('grupo', function($q) use ($gestionSeleccionada) {
                        $q->where('id_gestion', $gestionSeleccionada->id_gestion);
                    })
                    ->where('id_docente', $docente->id)
                    ->orderBy('dia_semana')
                    ->get()
                    ->sortBy(function($h) {
                        return $h->bloque->hora_inicio;
                    });

                $matriz = $this->construirMatrizSemanal($horarios);
            }
        }

        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
            ->orderBy('name')
            ->get();

        return view('publicacion.por-docente', compact('gestiones', 'gestionSeleccionada', 'docentes', 'docente', 'horarios', 'matriz'));
    }

    /**
     * Vista pública: Horarios por grupo/materia
     */
    public function porGrupo(Request $request)
    {
        $gestiones = Gestion::publicadas()->orderBy('fecha_inicio', 'desc')->get();
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        
        $gestionSeleccionada = null;
        $carreraSeleccionada = null;
        $grupos = collect();
        $grupo = null;
        $horarios = collect();
        $matriz = [];

        if ($request->filled('id_gestion')) {
            $gestionSeleccionada = Gestion::publicadas()->find($request->id_gestion);
        }

        if ($request->filled('id_carrera')) {
            $carreraSeleccionada = Carrera::find($request->id_carrera);
        }

        if ($gestionSeleccionada && $carreraSeleccionada) {
            $grupos = Grupo::with('materia')
                ->where('id_gestion', $gestionSeleccionada->id_gestion)
                ->whereHas('materia', function($q) use ($carreraSeleccionada) {
                    $q->where('id_carrera', $carreraSeleccionada->id_carrera);
                })
                ->orderBy('codigo_grupo')
                ->get();
        }

        if ($request->filled('id_grupo')) {
            $grupo = Grupo::with('materia.carrera')->find($request->id_grupo);
            
            if ($grupo) {
                $horarios = HorarioClase::with(['docente', 'aula', 'bloque'])
                    ->where('id_grupo', $grupo->id_grupo)
                    ->orderBy('dia_semana')
                    ->get()
                    ->sortBy(function($h) {
                        return $h->bloque->hora_inicio;
                    });

                $matriz = $this->construirMatrizSemanal($horarios);
            }
        }

        return view('publicacion.por-grupo', compact('gestiones', 'carreras', 'gestionSeleccionada', 'carreraSeleccionada', 'grupos', 'grupo', 'horarios', 'matriz'));
    }

    /**
     * Vista pública: Horarios por aula
     */
    public function porAula(Request $request)
    {
        $gestiones = Gestion::publicadas()->orderBy('fecha_inicio', 'desc')->get();
        $aulas = Aula::orderBy('codigo_aula')->get();
        
        $gestionSeleccionada = null;
        $aula = null;
        $horarios = collect();
        $matriz = [];

        if ($request->filled('id_gestion')) {
            $gestionSeleccionada = Gestion::publicadas()->find($request->id_gestion);
        }

        if ($request->filled('id_aula') && $gestionSeleccionada) {
            $aula = Aula::find($request->id_aula);
            
            if ($aula) {
                $horarios = HorarioClase::with(['grupo.materia', 'docente', 'bloque'])
                    ->whereHas('grupo', function($q) use ($gestionSeleccionada) {
                        $q->where('id_gestion', $gestionSeleccionada->id_gestion);
                    })
                    ->where('id_aula', $aula->id_aula)
                    ->orderBy('dia_semana')
                    ->get()
                    ->sortBy(function($h) {
                        return $h->bloque->hora_inicio;
                    });

                $matriz = $this->construirMatrizSemanal($horarios);
            }
        }

        return view('publicacion.por-aula', compact('gestiones', 'gestionSeleccionada', 'aulas', 'aula', 'horarios', 'matriz'));
    }

    /**
     * Maestro de oferta (todas las carreras de una gestión)
     */
    public function maestroOferta(Gestion $gestion)
    {
        if (!$gestion->publicada) {
            return redirect()
                ->route('publicacion.por-docente')
                ->with('error', 'Esta gestión no está publicada.');
        }

        $carreras = Carrera::with('facultad')->get();
        $datosPorCarrera = [];

        foreach ($carreras as $carrera) {
            $grupos = Grupo::with(['materia', 'docente'])
                ->where('id_gestion', $gestion->id_gestion)
                ->whereHas('materia', function($q) use ($carrera) {
                    $q->where('id_carrera', $carrera->id_carrera);
                })
                ->get();

            if ($grupos->isNotEmpty()) {
                $datosPorCarrera[$carrera->nombre_carrera] = $grupos;
            }
        }

        return view('publicacion.maestro-oferta', compact('gestion', 'datosPorCarrera'));
    }

    /**
     * PDF de horario de docente
     */
    public function pdfDocente(Gestion $gestion, User $docente)
    {
        if (!$gestion->publicada) {
            abort(403, 'Gestión no publicada');
        }

        $horarios = HorarioClase::with(['grupo.materia.carrera', 'aula', 'bloque'])
            ->whereHas('grupo', function($q) use ($gestion) {
                $q->where('id_gestion', $gestion->id_gestion);
            })
            ->where('id_docente', $docente->id)
            ->orderBy('dia_semana')
            ->get()
            ->sortBy(function($h) {
                return $h->bloque->hora_inicio;
            });

        $matriz = $this->construirMatrizSemanal($horarios);

        $pdf = PDF::loadView('publicacion.pdf.docente', compact('gestion', 'docente', 'horarios', 'matriz'));
        
        return $pdf->download("horario_{$docente->name}_{$gestion->nombre}.pdf");
    }

    /**
     * PDF de horario de grupo
     */
    public function pdfGrupo(Grupo $grupo)
    {
        $gestion = $grupo->gestion;
        
        if (!$gestion->publicada) {
            abort(403, 'Gestión no publicada');
        }

        $horarios = HorarioClase::with(['docente', 'aula', 'bloque'])
            ->where('id_grupo', $grupo->id_grupo)
            ->orderBy('dia_semana')
            ->get()
            ->sortBy(function($h) {
                return $h->bloque->hora_inicio;
            });

        $matriz = $this->construirMatrizSemanal($horarios);

        $pdf = PDF::loadView('publicacion.pdf.grupo', compact('gestion', 'grupo', 'horarios', 'matriz'));
        
        return $pdf->download("horario_{$grupo->codigo_grupo}_{$gestion->nombre}.pdf");
    }

    /**
     * PDF de horario de aula
     */
    public function pdfAula(Gestion $gestion, Aula $aula)
    {
        if (!$gestion->publicada) {
            abort(403, 'Gestión no publicada');
        }

        $horarios = HorarioClase::with(['grupo.materia', 'docente', 'bloque'])
            ->whereHas('grupo', function($q) use ($gestion) {
                $q->where('id_gestion', $gestion->id_gestion);
            })
            ->where('id_aula', $aula->id_aula)
            ->orderBy('dia_semana')
            ->get()
            ->sortBy(function($h) {
                return $h->bloque->hora_inicio;
            });

        $matriz = $this->construirMatrizSemanal($horarios);

        $pdf = PDF::loadView('publicacion.pdf.aula', compact('gestion', 'aula', 'horarios', 'matriz'));
        
        return $pdf->download("horario_aula_{$aula->codigo_aula}_{$gestion->nombre}.pdf");
    }

    /**
     * PDF Maestro de oferta completo
     */
    public function pdfMaestro(Gestion $gestion)
    {
        if (!$gestion->publicada) {
            abort(403, 'Gestión no publicada');
        }

        $carreras = Carrera::with('facultad')->get();
        $datosPorCarrera = [];

        foreach ($carreras as $carrera) {
            $grupos = Grupo::with(['materia', 'docente'])
                ->where('id_gestion', $gestion->id_gestion)
                ->whereHas('materia', function($q) use ($carrera) {
                    $q->where('id_carrera', $carrera->id_carrera);
                })
                ->get();

            // Siempre asignar grupos (aunque sea colección vacía)
            $carrera->grupos = $grupos;

            if ($grupos->isNotEmpty()) {
                foreach ($grupos as $grupo) {
                    $grupo->horarios = HorarioClase::with(['aula', 'bloque'])
                        ->where('id_grupo', $grupo->id_grupo)
                        ->orderBy('dia_semana')
                        ->get()
                        ->sortBy(function($h) {
                            return $h->bloque->hora_inicio;
                        });
                }
                
                $datosPorCarrera[$carrera->nombre] = $grupos;
            }
        }

        $pdf = PDF::loadView('publicacion.pdf.maestro', compact('gestion', 'carreras', 'datosPorCarrera'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->download("maestro_oferta_{$gestion->nombre}.pdf");
    }

    /**
     * Construir matriz semanal [dia][bloque][]
     */
    private function construirMatrizSemanal($horarios)
    {
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $matriz = [];

        foreach ($dias as $dia) {
            $matriz[$dia] = [];
        }

        foreach ($horarios as $horario) {
            $dia = $horario->dia;
            $bloqueKey = $horario->bloque->hora_inicio . '-' . $horario->bloque->hora_fin;
            
            if (!isset($matriz[$dia][$bloqueKey])) {
                $matriz[$dia][$bloqueKey] = [];
            }
            
            $matriz[$dia][$bloqueKey][] = $horario;
        }

        return $matriz;
    }
}
