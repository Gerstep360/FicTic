<?php

namespace App\Services;

use App\Models\HorarioClase;
use App\Models\Grupo;
use App\Models\Aula;
use App\Models\Bloque;
use App\Models\CargaDocente;
use App\Models\ReglaValidacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Validador de Horarios con Reglas Configurables
 * Detecta conflictos y violaciones de reglas académicas
 */
class ValidadorHorarios
{
    protected $conflictos = [];
    protected $advertencias = [];
    protected $reglas = [];
    protected $idGestion;
    protected $idCarrera;

    public function __construct($idGestion, $idCarrera = null)
    {
        $this->idGestion = $idGestion;
        $this->idCarrera = $idCarrera;
        $this->cargarReglas();
    }

    /**
     * Carga las reglas activas aplicables
     */
    protected function cargarReglas()
    {
        // Cargar reglas globales
        $this->reglas = ReglaValidacion::activas()->globales()->get();

        // Cargar reglas de carrera específica
        if ($this->idCarrera) {
            $reglaCarrera = ReglaValidacion::activas()->deCarrera($this->idCarrera)->get();
            $this->reglas = $this->reglas->merge($reglaCarrera);
        }
    }

    /**
     * Ejecuta todas las validaciones sobre los horarios
     */
    public function validar()
    {
        $this->conflictos = [];
        $this->advertencias = [];

        // Obtener horarios a validar
        $horarios = $this->obtenerHorarios();

        if ($horarios->isEmpty()) {
            return [
                'success' => true,
                'conflictos' => [],
                'advertencias' => [],
                'resumen' => [
                    'total_conflictos' => 0,
                    'criticos' => 0,
                    'altos' => 0,
                    'medios' => 0,
                    'bajos' => 0,
                ],
            ];
        }

        // Ejecutar validaciones según reglas activas
        foreach ($this->reglas as $regla) {
            $metodo = 'validar' . str_replace('_', '', ucwords($regla->codigo, '_'));
            
            if (method_exists($this, $metodo)) {
                $this->$metodo($horarios, $regla);
            }
        }

        // Validaciones base (siempre se ejecutan)
        $this->validarConflictosAula($horarios);
        $this->validarConflictosDocente($horarios);

        return [
            'success' => empty($this->conflictos),
            'conflictos' => $this->conflictos,
            'advertencias' => $this->advertencias,
            'resumen' => $this->generarResumen(),
        ];
    }

    /**
     * Obtiene los horarios a validar
     */
    protected function obtenerHorarios()
    {
        $query = HorarioClase::with(['grupo.materia.carrera', 'docente', 'aula', 'bloque'])
            ->whereHas('grupo', function($q) {
                $q->where('id_gestion', $this->idGestion);
                
                if ($this->idCarrera) {
                    $q->whereHas('materia', function($qq) {
                        $qq->where('id_carrera', $this->idCarrera);
                    });
                }
            });

        return $query->get();
    }

    /**
     * VALIDACIÓN 1: Conflictos de aula (mismo espacio físico al mismo tiempo)
     */
    protected function validarConflictosAula($horarios)
    {
        $ocupacion = [];

        foreach ($horarios as $horario) {
            $clave = "{$horario->dia_semana}_{$horario->id_bloque}_{$horario->id_aula}";
            
            if (!isset($ocupacion[$clave])) {
                $ocupacion[$clave] = [];
            }
            
            $ocupacion[$clave][] = $horario;
        }

        foreach ($ocupacion as $clave => $horariosEnConflicto) {
            if (count($horariosEnConflicto) > 1) {
                $this->registrarConflicto([
                    'tipo' => 'conflicto_aula',
                    'severidad' => 'critica',
                    'mensaje' => "Conflicto de aula: {$horariosEnConflicto[0]->aula->codigo}",
                    'detalles' => $this->construirDetallesConflictoAula($horariosEnConflicto),
                    'horarios_afectados' => $horariosEnConflicto->pluck('id_horario')->toArray(),
                    'sugerencia' => 'Reasignar una de las clases a otra aula disponible o cambiar el horario',
                ]);
            }
        }
    }

    /**
     * VALIDACIÓN 2: Conflictos de docente (mismo docente en dos lugares)
     */
    protected function validarConflictosDocente($horarios)
    {
        $ocupacion = [];

        foreach ($horarios as $horario) {
            $clave = "{$horario->dia_semana}_{$horario->id_bloque}_{$horario->id_docente}";
            
            if (!isset($ocupacion[$clave])) {
                $ocupacion[$clave] = [];
            }
            
            $ocupacion[$clave][] = $horario;
        }

        foreach ($ocupacion as $clave => $horariosEnConflicto) {
            if (count($horariosEnConflicto) > 1) {
                $this->registrarConflicto([
                    'tipo' => 'conflicto_docente',
                    'severidad' => 'critica',
                    'mensaje' => "Conflicto de docente: {$horariosEnConflicto[0]->docente->name}",
                    'detalles' => $this->construirDetallesConflictoDocente($horariosEnConflicto),
                    'horarios_afectados' => $horariosEnConflicto->pluck('id_horario')->toArray(),
                    'sugerencia' => 'Cambiar el horario de una de las clases o asignar otro docente',
                ]);
            }
        }
    }

    /**
     * VALIDACIÓN 3: Máximo de horas por día para docentes
     */
    protected function validarMaxhorasdia($horarios, $regla)
    {
        $maxHoras = $regla->parametros['max_horas'] ?? 4;
        $horasPorDocente = [];

        foreach ($horarios as $horario) {
            $clave = "{$horario->id_docente}_{$horario->dia_semana}";
            
            if (!isset($horasPorDocente[$clave])) {
                $horasPorDocente[$clave] = [
                    'count' => 0,
                    'docente' => $horario->docente,
                    'dia' => $horario->dia_semana,
                    'horarios' => [],
                ];
            }
            
            $horasPorDocente[$clave]['count']++;
            $horasPorDocente[$clave]['horarios'][] = $horario;
        }

        foreach ($horasPorDocente as $data) {
            if ($data['count'] > $maxHoras) {
                $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                
                $this->registrarConflicto([
                    'tipo' => 'exceso_horas_dia',
                    'severidad' => $regla->severidad,
                    'mensaje' => "{$data['docente']->name} excede el máximo de {$maxHoras} horas en {$dias[$data['dia']]}",
                    'detalles' => "Tiene {$data['count']} horas asignadas",
                    'horarios_afectados' => collect($data['horarios'])->pluck('id_horario')->toArray(),
                    'sugerencia' => 'Redistribuir clases a otros días de la semana',
                    'bloqueante' => $regla->bloqueante,
                ]);
            }
        }
    }

    /**
     * VALIDACIÓN 4: Descanso mínimo entre clases del mismo docente
     */
    protected function validarDescanso($horarios, $regla)
    {
        $minDescanso = $regla->parametros['minutos'] ?? 30;
        $horariosPorDocente = $horarios->groupBy('id_docente');

        foreach ($horariosPorDocente as $idDocente => $horariosDocente) {
            $porDia = $horariosDocente->groupBy('dia_semana');
            
            foreach ($porDia as $dia => $horariosDelDia) {
                $ordenados = $horariosDelDia->sortBy(function($h) {
                    return $h->bloque->hora_inicio;
                });

                $anterior = null;
                foreach ($ordenados as $actual) {
                    if ($anterior) {
                        $finAnterior = Carbon::parse($anterior->bloque->hora_fin);
                        $inicioActual = Carbon::parse($actual->bloque->hora_inicio);
                        $descansoMinutos = $finAnterior->diffInMinutes($inicioActual);

                        if ($descansoMinutos < $minDescanso) {
                            $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                            
                            $this->registrarConflicto([
                                'tipo' => 'descanso_insuficiente',
                                'severidad' => $regla->severidad,
                                'mensaje' => "Descanso insuficiente para {$actual->docente->name} el {$dias[$dia]}",
                                'detalles' => "Solo {$descansoMinutos} minutos entre clases (mínimo: {$minDescanso})",
                                'horarios_afectados' => [$anterior->id_horario, $actual->id_horario],
                                'sugerencia' => 'Reorganizar bloques horarios para dejar más espacio entre clases',
                                'bloqueante' => $regla->bloqueante,
                            ]);
                        }
                    }
                    $anterior = $actual;
                }
            }
        }
    }

    /**
     * VALIDACIÓN 5: Tipo de aula apropiado (laboratorio, estándar, etc.)
     */
    protected function validarTipoaula($horarios, $regla)
    {
        $requierenLab = $regla->parametros['materias_laboratorio'] ?? [];

        foreach ($horarios as $horario) {
            $nombreMateria = strtolower($horario->grupo->materia->nombre);
            $requiereLaboratorio = false;

            // Detectar si requiere laboratorio
            foreach (['laboratorio', 'práctica', 'practica', 'taller'] as $keyword) {
                if (str_contains($nombreMateria, $keyword)) {
                    $requiereLaboratorio = true;
                    break;
                }
            }

            // O si está en la lista específica
            if (in_array($horario->grupo->materia->id_materia, $requierenLab)) {
                $requiereLaboratorio = true;
            }

            if ($requiereLaboratorio && $horario->aula->tipo !== 'Laboratorio') {
                $this->registrarConflicto([
                    'tipo' => 'tipo_aula_inadecuado',
                    'severidad' => $regla->severidad,
                    'mensaje' => "El aula {$horario->aula->codigo} no es laboratorio",
                    'detalles' => "{$horario->grupo->materia->nombre} requiere laboratorio pero está en aula tipo {$horario->aula->tipo}",
                    'horarios_afectados' => [$horario->id_horario],
                    'sugerencia' => 'Reasignar a un aula de tipo Laboratorio',
                    'bloqueante' => $regla->bloqueante,
                ]);
            }
        }
    }

    /**
     * VALIDACIÓN 6: Capacidad del aula vs cupo del grupo
     */
    protected function validarCapacidad($horarios, $regla)
    {
        $margen = $regla->parametros['margen_porcentaje'] ?? 10;

        foreach ($horarios as $horario) {
            if ($horario->aula->capacidad && $horario->grupo->cupo) {
                $capacidadMaxima = $horario->aula->capacidad * (1 + $margen / 100);
                
                if ($horario->grupo->cupo > $capacidadMaxima) {
                    $this->registrarConflicto([
                        'tipo' => 'capacidad_excedida',
                        'severidad' => $regla->severidad,
                        'mensaje' => "Aula {$horario->aula->codigo} insuficiente para grupo {$horario->grupo->nombre_grupo}",
                        'detalles' => "Cupo: {$horario->grupo->cupo} estudiantes, Capacidad: {$horario->aula->capacidad}",
                        'horarios_afectados' => [$horario->id_horario],
                        'sugerencia' => 'Asignar un aula con mayor capacidad',
                        'bloqueante' => $regla->bloqueante,
                    ]);
                }
            }
        }
    }

    /**
     * VALIDACIÓN 7: Máximo de días consecutivos con clases
     */
    protected function validarContinuidad($horarios, $regla)
    {
        $maxDiasConsecutivos = $regla->parametros['max_dias'] ?? 5;
        $horariosPorDocente = $horarios->groupBy('id_docente');

        foreach ($horariosPorDocente as $idDocente => $horariosDocente) {
            $diasConClase = $horariosDocente->pluck('dia_semana')->unique()->sort()->values();
            
            $consecutivos = 1;
            $maxConsecutivos = 1;
            
            for ($i = 1; $i < $diasConClase->count(); $i++) {
                if ($diasConClase[$i] == $diasConClase[$i-1] + 1) {
                    $consecutivos++;
                    $maxConsecutivos = max($maxConsecutivos, $consecutivos);
                } else {
                    $consecutivos = 1;
                }
            }

            if ($maxConsecutivos > $maxDiasConsecutivos) {
                $docente = $horariosDocente->first()->docente;
                
                $this->registrarAdvertencia([
                    'tipo' => 'muchos_dias_consecutivos',
                    'severidad' => $regla->severidad,
                    'mensaje' => "{$docente->name} tiene {$maxConsecutivos} días consecutivos de clase",
                    'detalles' => "Máximo recomendado: {$maxDiasConsecutivos} días",
                    'sugerencia' => 'Considerar distribuir con días de descanso intermedios',
                ]);
            }
        }
    }

    /**
     * VALIDACIÓN 8: Excede carga contratada del docente
     */
    protected function validarCargacontratada($horarios, $regla)
    {
        $horasPorDocente = $horarios->groupBy('id_docente')->map->count();

        foreach ($horasPorDocente as $idDocente => $horasAsignadas) {
            $carga = CargaDocente::where('id_docente', $idDocente)
                ->where('id_gestion', $this->idGestion)
                ->when($this->idCarrera, fn($q) => $q->where('id_carrera', $this->idCarrera))
                ->first();

            if ($carga && $horasAsignadas > $carga->horas_contratadas) {
                $docente = $horarios->where('id_docente', $idDocente)->first()->docente;
                
                $this->registrarConflicto([
                    'tipo' => 'exceso_carga_contratada',
                    'severidad' => $regla->severidad,
                    'mensaje' => "{$docente->name} excede su carga contratada",
                    'detalles' => "Asignadas: {$horasAsignadas} horas, Contratadas: {$carga->horas_contratadas} horas",
                    'sugerencia' => 'Redistribuir clases a otros docentes o actualizar el contrato',
                    'bloqueante' => $regla->bloqueante,
                ]);
            }
        }
    }

    /**
     * Registra un conflicto
     */
    protected function registrarConflicto($conflicto)
    {
        $this->conflictos[] = array_merge([
            'id' => count($this->conflictos) + 1,
            'timestamp' => now()->toIso8601String(),
        ], $conflicto);
    }

    /**
     * Registra una advertencia
     */
    protected function registrarAdvertencia($advertencia)
    {
        $this->advertencias[] = array_merge([
            'id' => count($this->advertencias) + 1,
            'timestamp' => now()->toIso8601String(),
        ], $advertencia);
    }

    /**
     * Construye detalles del conflicto de aula
     */
    protected function construirDetallesConflictoAula($horarios)
    {
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $h = $horarios->first();
        $detalles = "El aula {$h->aula->codigo} está asignada a:\n";
        
        foreach ($horarios as $horario) {
            $detalles .= "- {$horario->grupo->materia->nombre} (Grupo {$horario->grupo->nombre_grupo}) con {$horario->docente->name}\n";
        }
        
        $detalles .= "En {$dias[$h->dia_semana]} de " . 
            Carbon::parse($h->bloque->hora_inicio)->format('H:i') . " a " .
            Carbon::parse($h->bloque->hora_fin)->format('H:i');
        
        return $detalles;
    }

    /**
     * Construye detalles del conflicto de docente
     */
    protected function construirDetallesConflictoDocente($horarios)
    {
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $h = $horarios->first();
        $detalles = "{$h->docente->name} está asignado a:\n";
        
        foreach ($horarios as $horario) {
            $detalles .= "- {$horario->grupo->materia->nombre} (Grupo {$horario->grupo->nombre_grupo}) en {$horario->aula->codigo}\n";
        }
        
        $detalles .= "En {$dias[$h->dia_semana]} de " . 
            Carbon::parse($h->bloque->hora_inicio)->format('H:i') . " a " .
            Carbon::parse($h->bloque->hora_fin)->format('H:i');
        
        return $detalles;
    }

    /**
     * Genera resumen de conflictos
     */
    protected function generarResumen()
    {
        $criticos = 0;
        $altos = 0;
        $medios = 0;
        $bajos = 0;

        foreach ($this->conflictos as $conflicto) {
            switch ($conflicto['severidad']) {
                case 'critica': $criticos++; break;
                case 'alta': $altos++; break;
                case 'media': $medios++; break;
                case 'baja': $bajos++; break;
            }
        }

        return [
            'total_conflictos' => count($this->conflictos),
            'total_advertencias' => count($this->advertencias),
            'criticos' => $criticos,
            'altos' => $altos,
            'medios' => $medios,
            'bajos' => $bajos,
            'bloqueantes' => collect($this->conflictos)->where('bloqueante', true)->count(),
        ];
    }
}
