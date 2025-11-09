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
 * Implementa algoritmo de backtracking con restricciones
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
    protected $maxIntentos = 10000;

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
     * Intenta asignar un grupo a un horario válido
     */
    protected function asignarGrupo($grupo)
    {
        if (!$grupo->id_docente) {
            return false; // No se puede asignar sin docente
        }

        // Calcular cuántas horas necesita el grupo (asumiendo que cada grupo necesita ciertas horas semanales)
        // Por defecto: 2 sesiones de 2 horas cada una (ajustar según tu lógica)
        $horasNecesarias = $this->calcularHorasNecesarias($grupo);
        $sesionesAsignadas = 0;
        $intentosGrupo = 0;
        $maxIntentosGrupo = $this->configuracion['intentos_por_grupo'];

        while ($sesionesAsignadas < $horasNecesarias && $intentosGrupo < $maxIntentosGrupo) {
            $intentosGrupo++;
            $this->intentos++;

            if ($this->intentos > $this->maxIntentos) {
                break;
            }

            // Seleccionar dia y bloque aleatoriamente
            $dia = $this->seleccionarDia($grupo);
            $bloque = $this->seleccionarBloque($grupo, $dia);
            $aula = $this->seleccionarAula($grupo);

            if (!$dia || !$bloque || !$aula) {
                continue;
            }

            // Validar conflictos
            if ($this->validarAsignacion($grupo, $dia, $bloque->id_bloque, $aula->id_aula)) {
                // Asignar
                $this->registrarAsignacion($grupo, $dia, $bloque->id_bloque, $aula->id_aula);
                $sesionesAsignadas++;
            }
        }

        return $sesionesAsignadas > 0;
    }

    /**
     * Calcula las horas semanales necesarias para un grupo
     */
    protected function calcularHorasNecesarias($grupo)
    {
        // Heurística: cada grupo necesita 2-4 sesiones por semana
        // Ajustar según créditos de la materia o configuración
        if ($grupo->materia && $grupo->materia->creditos) {
            return min(4, max(2, $grupo->materia->creditos));
        }
        return 2; // Default: 2 sesiones
    }

    /**
     * Selecciona un día apropiado para el grupo
     */
    protected function seleccionarDia($grupo)
    {
        // Aplicar preferencias si están configuradas
        if ($this->configuracion['respetar_preferencias']) {
            // Aquí podrías implementar lógica de preferencias por día
        }

        // Seleccionar día con menos carga para el docente
        $diasDisponibles = $this->dias;
        
        if ($this->configuracion['balancear_carga_diaria']) {
            usort($diasDisponibles, function($a, $b) use ($grupo) {
                $cargaA = $this->contarAsignacionesDocente($grupo->id_docente, $a);
                $cargaB = $this->contarAsignacionesDocente($grupo->id_docente, $b);
                return $cargaA <=> $cargaB;
            });
        } else {
            shuffle($diasDisponibles);
        }

        return $diasDisponibles[0] ?? null;
    }

    /**
     * Selecciona un bloque horario apropiado
     */
    protected function seleccionarBloque($grupo, $dia)
    {
        $bloquesDisponibles = $this->bloques->shuffle();

        // Aplicar preferencias de horario
        if ($this->configuracion['respetar_preferencias']) {
            $docente = $grupo->id_docente;
            
            if (in_array($docente, $this->configuracion['preferir_manana'])) {
                // Priorizar bloques de mañana (antes de 12:00)
                $bloquesDisponibles = $bloquesDisponibles->sortBy(function($bloque) {
                    return strtotime($bloque->hora_inicio) < strtotime('12:00:00') ? 0 : 1;
                });
            } elseif (in_array($docente, $this->configuracion['preferir_tarde'])) {
                // Priorizar bloques de tarde (después de 12:00)
                $bloquesDisponibles = $bloquesDisponibles->sortBy(function($bloque) {
                    return strtotime($bloque->hora_inicio) >= strtotime('12:00:00') ? 0 : 1;
                });
            }
        }

        // Minimizar huecos: preferir bloques contiguos a clases ya asignadas
        if ($this->configuracion['minimizar_huecos']) {
            $bloquesDisponibles = $bloquesDisponibles->sortBy(function($bloque) use ($grupo, $dia) {
                return $this->tieneClaseCercana($grupo->id_docente, $dia, $bloque->id_bloque) ? 0 : 1;
            });
        }

        return $bloquesDisponibles->first();
    }

    /**
     * Selecciona un aula apropiada para el grupo
     */
    protected function seleccionarAula($grupo)
    {
        // Filtrar aulas por capacidad si el grupo tiene cupo definido
        $aulasDisponibles = $this->aulas;
        
        if ($grupo->cupo) {
            $aulasDisponibles = $aulasDisponibles->filter(function($aula) use ($grupo) {
                return !$aula->capacidad || $aula->capacidad >= $grupo->cupo;
            });
        }

        // Priorizar aulas del tipo adecuado
        $aulasDisponibles = $aulasDisponibles->sortBy(function($aula) {
            // Aulas estándar primero, laboratorios/auditorio después
            $prioridad = ['Estándar' => 1, 'Laboratorio' => 2, 'Auditorio' => 3];
            return $prioridad[$aula->tipo] ?? 4;
        });

        return $aulasDisponibles->first();
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

        // 3. Validar carga horaria del docente
        if (isset($this->cargasDocentes[$grupo->id_docente])) {
            $carga = $this->cargasDocentes[$grupo->id_docente];
            $horasAsignadas = $this->horasAsignadasDocente[$grupo->id_docente] ?? 0;
            
            if ($horasAsignadas >= $carga->horas_contratadas) {
                return false;
            }
        }

        // 4. Validar máximo de horas por día
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
     * Verifica si el docente tiene clases cercanas (para minimizar huecos)
     */
    protected function tieneClaseCercana($idDocente, $dia, $idBloque)
    {
        foreach ($this->asignaciones as $asig) {
            if ($asig['id_docente'] == $idDocente && $asig['dia_semana'] == $dia) {
                // Si hay una clase en el bloque anterior o siguiente
                if (abs($asig['id_bloque'] - $idBloque) == 1) {
                    return true;
                }
            }
        }
        return false;
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
