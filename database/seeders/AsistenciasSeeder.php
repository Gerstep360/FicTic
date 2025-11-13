<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\Bloque;
use App\Models\HorarioClase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsistenciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Este seeder genera asistencias para los primeros 10 docentes
     * basándose en sus horarios reales de clase.
     * 
     * Validaciones:
     * - Solo crea asistencias para docentes con horarios asignados
     * - Valida que existan bloques con horarios definidos
     * - Genera asistencias de los últimos 7 días
     * - Determina tardanza basándose en la hora de entrada vs hora del bloque
     * - Marca entrada y salida para cada clase
     * 
     * Para ejecutar: php artisan db:seed --class=AsistenciasSeeder
     */
    public function run(): void
    {
        $this->command->info('🔄 Iniciando generación de asistencias...');
        
        // Validar que existan docentes
        $totalDocentes = User::role(['Docente', 'Coordinador', 'Director'])->count();
        if ($totalDocentes === 0) {
            $this->command->error('❌ No hay docentes en el sistema.');
            return;
        }
        
        // Validar que existan bloques
        $bloques = Bloque::all();
        if ($bloques->isEmpty()) {
            $this->command->error('❌ No hay bloques horarios definidos.');
            return;
        }
        
        // Validar que existan horarios
        $totalHorarios = HorarioClase::count();
        if ($totalHorarios === 0) {
            $this->command->error('❌ No hay horarios de clase asignados.');
            return;
        }
        
        $this->command->info("✅ Sistema validado:");
        $this->command->info("   - Docentes: {$totalDocentes}");
        $this->command->info("   - Bloques: {$bloques->count()}");
        $this->command->info("   - Horarios: {$totalHorarios}");
        $this->command->newLine();
        
        // Obtener los primeros 10 docentes con horarios asignados
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
                        ->whereHas('horarioClasesComoDocente')
                        ->with('horarioClasesComoDocente.bloque')
                        ->limit(10)
                        ->get();
        
        if ($docentes->isEmpty()) {
            $this->command->error('❌ Ninguno de los primeros 10 docentes tiene horarios asignados.');
            return;
        }
        
        $this->command->info("📋 Generando asistencias para {$docentes->count()} docentes...");
        $this->command->newLine();
        
        $asistenciasCreadas = 0;
        $hoy = Carbon::now();
        
        // Generar asistencias para los últimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subDays($i);
            $diaSemana = $fecha->dayOfWeekIso; // 1=Lunes, 7=Domingo
            
            // Saltar fines de semana
            if ($diaSemana >= 6) {
                continue;
            }
            
            $this->command->info("📅 {$fecha->format('d/m/Y')} - {$fecha->locale('es')->dayName}");
            
            foreach ($docentes as $docente) {
                // Obtener horarios del docente para este día
                $horariosDelDia = HorarioClase::with(['bloque', 'grupo.materia', 'aula'])
                                              ->where('id_docente', $docente->id)
                                              ->where('dia_semana', $diaSemana)
                                              ->get();
                
                if ($horariosDelDia->isEmpty()) {
                    continue;
                }
                
                foreach ($horariosDelDia as $horario) {
                    // Determinar si el docente asistió (90% de probabilidad)
                    $asistio = rand(1, 100) <= 90;
                    
                    if (!$asistio) {
                        // 10% de probabilidad de falta
                        continue;
                    }
                    
                    // Obtener hora de inicio del bloque
                    $horaBloque = Carbon::parse($horario->bloque->hora_inicio);
                    
                    // ENTRADA
                    // Determinar si llegó tarde, a tiempo o temprano
                    $probabilidad = rand(1, 100);
                    
                    if ($probabilidad <= 70) {
                        // 70% llega a tiempo (dentro de 5 minutos de tolerancia)
                        $minutosVariacion = rand(-5, 5);
                        $estado = 'PRESENTE';
                    } elseif ($probabilidad <= 85) {
                        // 15% llega temprano (5-15 minutos antes)
                        $minutosVariacion = rand(-15, -5);
                        $estado = 'PRESENTE';
                    } else {
                        // 15% llega tarde (6-30 minutos tarde)
                        $minutosVariacion = rand(6, 30);
                        $estado = 'TARDANZA';
                    }
                    
                    $fechaHoraEntrada = $fecha->copy()
                                              ->setTimeFrom($horaBloque)
                                              ->addMinutes($minutosVariacion);
                    
                    // Verificar que no exista ya esta asistencia
                    $existeEntrada = Asistencia::where('id_docente', $docente->id)
                                               ->where('id_horario', $horario->id_horario)
                                               ->whereDate('fecha_hora', $fecha)
                                               ->where('tipo_marca', 'ENTRADA')
                                               ->exists();
                    
                    if (!$existeEntrada) {
                        Asistencia::create([
                            'id_docente' => $docente->id,
                            'id_horario' => $horario->id_horario,
                            'fecha_hora' => $fechaHoraEntrada,
                            'tipo_marca' => 'ENTRADA',
                            'estado' => $estado,
                            'es_manual' => false,
                            'observacion' => null,
                        ]);
                        
                        $asistenciasCreadas++;
                    }
                    
                    // SALIDA
                    // 95% registra salida
                    if (rand(1, 100) <= 95) {
                        $horaFin = Carbon::parse($horario->bloque->hora_fin);
                        
                        // Salida con variación de ±10 minutos
                        $minutosVariacionSalida = rand(-10, 10);
                        $fechaHoraSalida = $fecha->copy()
                                                 ->setTimeFrom($horaFin)
                                                 ->addMinutes($minutosVariacionSalida);
                        
                        $existeSalida = Asistencia::where('id_docente', $docente->id)
                                                  ->where('id_horario', $horario->id_horario)
                                                  ->whereDate('fecha_hora', $fecha)
                                                  ->where('tipo_marca', 'SALIDA')
                                                  ->exists();
                        
                        if (!$existeSalida) {
                            Asistencia::create([
                                'id_docente' => $docente->id,
                                'id_horario' => $horario->id_horario,
                                'fecha_hora' => $fechaHoraSalida,
                                'tipo_marca' => 'SALIDA',
                                'estado' => 'PRESENTE',
                                'es_manual' => false,
                                'observacion' => null,
                            ]);
                            
                            $asistenciasCreadas++;
                        }
                    }
                }
            }
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeder completado exitosamente");
        $this->command->info("📊 Total de asistencias creadas: {$asistenciasCreadas}");
        $this->command->newLine();
        
        // Estadísticas adicionales
        $totalEntradas = Asistencia::where('tipo_marca', 'ENTRADA')->count();
        $totalSalidas = Asistencia::where('tipo_marca', 'SALIDA')->count();
        $totalTardanzas = Asistencia::where('estado', 'TARDANZA')->count();
        $totalPresentes = Asistencia::where('estado', 'PRESENTE')->count();
        
        $this->command->info("📈 Estadísticas generales:");
        $this->command->info("   - Total entradas: {$totalEntradas}");
        $this->command->info("   - Total salidas: {$totalSalidas}");
        $this->command->info("   - Presentes: {$totalPresentes}");
        $this->command->info("   - Tardanzas: {$totalTardanzas}");
    }
}
