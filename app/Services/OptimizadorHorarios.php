<?php

namespace App\Services;

use App\Models\Grupo;
use App\Models\Aula;
use App\Models\Bloque;
use App\Models\HorarioClase;
use App\Models\CargaDocente;
use Illuminate\Support\Facades\DB;

/**
 * Motor de optimización para generación automática de horarios
 * Implementa algoritmo sistemático con patrones realistas
 */
class OptimizadorHorarios
{
    protected $idGestion;
    protected $idCarrera;
    protected $configuracion;
    protected $grupos;
    protected $aulas;
    protected $bloques;
    protected $dias = [1, 2, 3, 4, 5, 6]; // Lunes a Sábado
    protected $asignaciones = [];
    protected $ocupacionAulas = []; // [dia][bloque][aula_id] => true
    protected $ocupacionDocentes = []; // [dia][bloque][docente_id] => true
    protected $horasAsignadasDocente = []; // [docente_id] => count
    protected $cargasDocentes = []; // [docente_id] => CargaDocente
    protected $intentos = 0;
    protected $maxIntentos = 50000;
    protected $patronesDias = [
        'lun_mie_vie' => [1, 3, 5], // Lunes, Miércoles, Viernes
        'mar_jue' => [2, 4],         // Martes, Jueves
        'mar_jue_sab' => [2, 4, 6],  // Martes, Jueves, Sábado (raro)
        'sabado' => [6],             // Solo sábado (muy raro)
    ];
    protected $aulaIndex = 0; // Para rotación de aulas

    public function __construct($idGestion, $idCarrera = null, $configuracion = [])
    {
        $this->idGestion = $idGestion;
        $this->idCarrera = $idCarrera;
        $this->configuracion = array_merge([
            'minimizar_huecos' => true,
            'balancear_carga_diaria' => true,
            'respetar_preferencias' => true,
            'preferir_manana' => [],
            'preferir_tarde' => [],
            'max_horas_dia_docente' => 4,
            'min_descanso_entre_clases' => 0, // minutos
            'intentos_por_grupo' => 100,
        ], $configuracion);
    }

    /**
     * Ejecuta la generación automática de horarios
     */
    public function generar()
    {
        $this->cargarDatos();
        
        if ($this->grupos->isEmpty()) {
            return [
                'success' => false,
                'mensaje' => 'No hay grupos disponibles para asignar en esta gestión/carrera',
                'metricas' => $this->calcularMetricas(),
            ];
        }

        // Ordenar grupos por prioridad (materias con menos opciones primero)
        $this->grupos = $this->grupos->sortByDesc(function($grupo) {
            // Grupos con docente asignado tienen prioridad
            return $grupo->id_docente ? 1 : 0;
        });

        // Intentar asignar cada grupo
        foreach ($this->grupos as $grupo) {
            if (!$this->asignarGrupo($grupo)) {
                // Si no se pudo asignar, continuar (se reportará en métricas)
            }
        }

        $metricas = $this->calcularMetricas();

        return [
            'success' => $metricas['grupos_asignados'] > 0,
            'asignaciones' => $this->asignaciones,
            'metricas' => $metricas,
            'mensaje' => $this->generarMensaje($metricas),
        ];
    }

    /**
     * Carga los datos necesarios para la generación
     */
    protected function cargarDatos()
    {
        // Cargar grupos
        $query = Grupo::with(['materia.carrera', 'docente'])
            ->where('id_gestion', $this->idGestion);
        
        if ($this->idCarrera) {
            $query->whereHas('materia', function($q) {
                $q->where('id_carrera', $this->idCarrera);
            });
        }
        
        $this->grupos = $query->get();

        // Cargar aulas (soft deletes ya filtra las eliminadas automáticamente)
        $this->aulas = Aula::orderBy('codigo')->get();

        // Cargar bloques horarios
        $this->bloques = Bloque::orderBy('hora_inicio')->get();

        // Cargar cargas docentes
        $cargas = CargaDocente::where('id_gestion', $this->idGestion)
            ->when($this->idCarrera, fn($q) => $q->where('id_carrera', $this->idCarrera))
            ->get();
        
        foreach ($cargas as $carga) {
            $this->cargasDocentes[$carga->id_docente] = $carga;
            $this->horasAsignadasDocente[$carga->id_docente] = 0;
        }

        // Inicializar matrices de ocupación
        foreach ($this->dias as $dia) {
            foreach ($this->bloques as $bloque) {
                $this->ocupacionAulas[$dia][$bloque->id_bloque] = [];
                $this->ocupacionDocentes[$dia][$bloque->id_bloque] = [];
            }
        }
    }

    /**
     * Intenta asignar un grupo a un horario válido usando patrones realistas
     */
    protected function asignarGrupo($grupo)
    {
        if (!$grupo->id_docente) {
            return false; // No se puede asignar sin docente
        }

        // Seleccionar patrón de días (prioritariamente Lun-Mie-Vie y Mar-Jue)
        $patronesPreferidos = [
            ['dias' => $this->patronesDias['lun_mie_vie'], 'peso' => 50], // 50% probabilidad
            ['dias' => $this->patronesDias['mar_jue'], 'peso' => 40],      // 40% probabilidad
            ['dias' => $this->patronesDias['mar_jue_sab'], 'peso' => 7],   // 7% probabilidad
            ['dias' => $this->patronesDias['sabado'], 'peso' => 3],        // 3% probabilidad
        ];

        // Probar cada patrón hasta encontrar uno que funcione
        foreach ($patronesPreferidos as $patron) {
            $diasPatron = $patron['dias'];
            $exito = $this->intentarAsignarConPatron($grupo, $diasPatron);
            
            if ($exito) {
                return true;
            }
        }

        // Si ningún patrón funcionó, intentar asignación individual en cualquier día
        return $this->intentarAsignacionIndividual($grupo);
    }

    /**
     * Intenta asignar un grupo siguiendo un patrón específico de días
     */
    protected function intentarAsignarConPatron($grupo, $diasPatron)
    {
        $horasNecesarias = min(count($diasPatron), $this->calcularHorasNecesarias($grupo));
        
        // Probar cada bloque horario
        foreach ($this->bloques as $bloque) {
            $asignacionesTemp = [];
            $todosExitosos = true;

            // Intentar asignar en todos los días del patrón con el mismo bloque
            for ($i = 0; $i < $horasNecesarias; $i++) {
                $dia = $diasPatron[$i];
                $aula = $this->seleccionarAulaSistematica($grupo);

                if (!$aula) {
                    $todosExitosos = false;
                    break;
                }

                // Validar si esta asignación es posible
                if ($this->validarAsignacion($grupo, $dia, $bloque->id_bloque, $aula->id_aula)) {
                    $asignacionesTemp[] = [
                        'dia' => $dia,
                        'bloque' => $bloque->id_bloque,
                        'aula' => $aula->id_aula,
                    ];
                } else {
                    $todosExitosos = false;
                    break;
                }
            }

            // Si todas las asignaciones del patrón son válidas, registrarlas
            if ($todosExitosos && count($asignacionesTemp) > 0) {
                foreach ($asignacionesTemp as $asig) {
                    $this->registrarAsignacion($grupo, $asig['dia'], $asig['bloque'], $asig['aula']);
                }
                return true;
            }

            $this->intentos++;
            if ($this->intentos > $this->maxIntentos) {
                return false;
            }
        }

        return false;
    }

    /**
     * Intenta asignar individualmente en cualquier combinación día-bloque disponible
     */
    protected function intentarAsignacionIndividual($grupo)
    {
        $horasNecesarias = $this->calcularHorasNecesarias($grupo);
        $sesionesAsignadas = 0;
        $intentosGrupo = 0;
        $maxIntentosGrupo = $this->configuracion['intentos_por_grupo'];

        // Probar todas las combinaciones día-bloque-aula sistemáticamente
        foreach ($this->dias as $dia) {
            if ($sesionesAsignadas >= $horasNecesarias) {
                break;
            }

            foreach ($this->bloques as $bloque) {
                if ($sesionesAsignadas >= $horasNecesarias) {
                    break;
                }

                $aula = $this->seleccionarAulaSistematica($grupo);

                if (!$aula) {
                    continue;
                }

                if ($this->validarAsignacion($grupo, $dia, $bloque->id_bloque, $aula->id_aula)) {
                    $this->registrarAsignacion($grupo, $dia, $bloque->id_bloque, $aula->id_aula);
                    $sesionesAsignadas++;
                }

                $intentosGrupo++;
                $this->intentos++;

                if ($intentosGrupo >= $maxIntentosGrupo || $this->intentos > $this->maxIntentos) {
                    break 2;
                }
            }
        }

        return $sesionesAsignadas > 0;
    }

    /**
     * Selecciona un aula de forma sistemática (rotación) para distribuir equitativamente
     */
    protected function seleccionarAulaSistematica($grupo)
    {
        if ($this->aulas->isEmpty()) {
            return null;
        }

        // Filtrar aulas por capacidad
        $aulasValidas = $this->aulas->filter(function($aula) use ($grupo) {
            return !$aula->capacidad || $aula->capacidad >= ($grupo->cupo ?? 0);
        });

        if ($aulasValidas->isEmpty()) {
            $aulasValidas = $this->aulas; // Si no hay aulas válidas, usar todas
        }

        $aulasArray = $aulasValidas->values();
        
        // Rotación circular para distribuir equitativamente
        $aula = $aulasArray[$this->aulaIndex % $aulasArray->count()];
        $this->aulaIndex++;

        return $aula;
    }

    /**
     * Calcula las horas semanales necesarias para un grupo
     */
    protected function calcularHorasNecesarias($grupo)
    {
        // Heurística mejorada: 
        // - Materias de 3 créditos o menos: 2 sesiones semanales
        // - Materias de 4-5 créditos: 3 sesiones semanales
        // - Materias de 6+ créditos: 4 sesiones semanales
        if ($grupo->materia && $grupo->materia->creditos) {
            if ($grupo->materia->creditos <= 3) {
                return 2;
            } elseif ($grupo->materia->creditos <= 5) {
                return 3;
            } else {
                return 4;
            }
        }
        return 2; // Default: 2 sesiones (Mar-Jue típicamente)
    }

    /**
     * Valida que una asignación no tenga conflictos
     */
    protected function validarAsignacion($grupo, $dia, $idBloque, $idAula)
    {
        // 1. Validar que el aula no esté ocupada
        if (isset($this->ocupacionAulas[$dia][$idBloque][$idAula])) {
            return false;
        }

        // 2. Validar que el docente no esté ocupado
        if (isset($this->ocupacionDocentes[$dia][$idBloque][$grupo->id_docente])) {
            return false;
        }

        // 3. Validar carga horaria del docente (si existe)
        if (isset($this->cargasDocentes[$grupo->id_docente])) {
            $carga = $this->cargasDocentes[$grupo->id_docente];
            $horasAsignadas = $this->horasAsignadasDocente[$grupo->id_docente] ?? 0;
            
            if ($horasAsignadas >= $carga->horas_contratadas) {
                return false;
            }
        }

        // 4. Validar máximo de horas por día (evitar sobrecarga diaria)
        $horasEsteDia = $this->contarAsignacionesDocente($grupo->id_docente, $dia);
        if ($horasEsteDia >= $this->configuracion['max_horas_dia_docente']) {
            return false;
        }

        return true;
    }

    /**
     * Registra una asignación válida
     */
    protected function registrarAsignacion($grupo, $dia, $idBloque, $idAula)
    {
        $this->asignaciones[] = [
            'id_grupo' => $grupo->id_grupo,
            'dia_semana' => $dia,
            'id_bloque' => $idBloque,
            'id_aula' => $idAula,
            'id_docente' => $grupo->id_docente,
        ];

        $this->ocupacionAulas[$dia][$idBloque][$idAula] = true;
        $this->ocupacionDocentes[$dia][$idBloque][$grupo->id_docente] = true;
        $this->horasAsignadasDocente[$grupo->id_docente] = 
            ($this->horasAsignadasDocente[$grupo->id_docente] ?? 0) + 1;
    }

    /**
     * Cuenta asignaciones del docente en un día específico
     */
    protected function contarAsignacionesDocente($idDocente, $dia)
    {
        $count = 0;
        foreach ($this->asignaciones as $asig) {
            if ($asig['id_docente'] == $idDocente && $asig['dia_semana'] == $dia) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Calcula métricas del resultado
     */
    protected function calcularMetricas()
    {
        $gruposConDocente = $this->grupos->filter(fn($g) => $g->id_docente)->count();
        $gruposAsignados = collect($this->asignaciones)->unique('id_grupo')->count();
        
        // Calcular puntuación de optimización (0-100)
        $puntuacion = 0;
        if ($gruposConDocente > 0) {
            $puntuacion = ($gruposAsignados / $gruposConDocente) * 100;
            
            // Bonificación por minimizar huecos
            if ($this->configuracion['minimizar_huecos']) {
                $huecos = $this->contarHuecos();
                $penalizacion = min(20, $huecos * 2);
                $puntuacion = max(0, $puntuacion - $penalizacion);
            }
        }

        return [
            'total_grupos' => $this->grupos->count(),
            'grupos_con_docente' => $gruposConDocente,
            'grupos_asignados' => $gruposAsignados,
            'grupos_sin_asignar' => $gruposConDocente - $gruposAsignados,
            'conflictos_detectados' => 0, // Los conflictos se previenen durante la asignación
            'puntuacion_optimizacion' => round($puntuacion, 2),
            'intentos_realizados' => $this->intentos,
        ];
    }

    /**
     * Cuenta huecos en los horarios de los docentes
     */
    protected function contarHuecos()
    {
        $huecos = 0;
        $docentesUnicos = collect($this->asignaciones)->pluck('id_docente')->unique();
        
        foreach ($docentesUnicos as $idDocente) {
            foreach ($this->dias as $dia) {
                $bloques = collect($this->asignaciones)
                    ->where('id_docente', $idDocente)
                    ->where('dia_semana', $dia)
                    ->pluck('id_bloque')
                    ->sort()
                    ->values();
                
                if ($bloques->count() > 1) {
                    for ($i = 0; $i < $bloques->count() - 1; $i++) {
                        $diferencia = $bloques[$i + 1] - $bloques[$i];
                        if ($diferencia > 1) {
                            $huecos += ($diferencia - 1);
                        }
                    }
                }
            }
        }
        
        return $huecos;
    }

    /**
     * Genera mensaje descriptivo del resultado
     */
    protected function generarMensaje($metricas)
    {
        $msgs = [];
        
        if ($metricas['grupos_asignados'] == 0) {
            $msgs[] = "No se pudo asignar ningún grupo. Verifica que los grupos tengan docentes asignados y que haya aulas disponibles.";
        } elseif ($metricas['grupos_sin_asignar'] > 0) {
            $msgs[] = "Se asignaron {$metricas['grupos_asignados']} de {$metricas['grupos_con_docente']} grupos. Quedan {$metricas['grupos_sin_asignar']} grupos sin asignar.";
            $msgs[] = "Intenta ajustar la configuración o asignar manualmente los grupos restantes.";
        } else {
            $msgs[] = "¡Generación completada exitosamente! Se asignaron todos los {$metricas['grupos_asignados']} grupos.";
        }
        
        if ($this->intentos >= $this->maxIntentos) {
            $msgs[] = "Se alcanzó el límite de intentos ({$this->maxIntentos}). Algunos grupos no pudieron asignarse.";
        }
        
        $msgs[] = "Puntuación de optimización: {$metricas['puntuacion_optimizacion']}/100";
        
        return implode(' ', $msgs);
    }
}
