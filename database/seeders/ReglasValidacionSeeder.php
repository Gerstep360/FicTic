<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReglaValidacion;

class ReglasValidacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reglasDefecto = [
            [
                'codigo' => 'CONFLICTO_AULA',
                'nombre' => 'Conflicto de Aula',
                'descripcion' => 'Verifica que no existan dos grupos asignados a la misma aula en el mismo horario',
                'categoria' => 'otras',
                'severidad' => 'critica',
                'activa' => true,
                'bloqueante' => true,
                'parametros' => null,
            ],
            [
                'codigo' => 'CONFLICTO_DOCENTE',
                'nombre' => 'Conflicto de Docente',
                'descripcion' => 'Verifica que un docente no esté asignado en dos lugares simultáneamente',
                'categoria' => 'otras',
                'severidad' => 'critica',
                'activa' => true,
                'bloqueante' => true,
                'parametros' => null,
            ],
            [
                'codigo' => 'MAX_HORAS_DIA',
                'nombre' => 'Máximo de Horas por Día',
                'descripcion' => 'Limita la cantidad máxima de horas que un docente puede impartir en un solo día',
                'categoria' => 'carga_docente',
                'severidad' => 'alta',
                'activa' => true,
                'bloqueante' => false,
                'parametros' => ['max_horas' => 4],
            ],
            [
                'codigo' => 'DESCANSO',
                'nombre' => 'Tiempo de Descanso',
                'descripcion' => 'Asegura un tiempo mínimo de descanso entre clases consecutivas del mismo docente',
                'categoria' => 'descanso',
                'severidad' => 'media',
                'activa' => true,
                'bloqueante' => false,
                'parametros' => ['minutos' => 30],
            ],
            [
                'codigo' => 'TIPO_AULA',
                'nombre' => 'Validación de Tipo de Aula',
                'descripcion' => 'Verifica que las materias de laboratorio estén asignadas en aulas tipo laboratorio',
                'categoria' => 'tipo_aula',
                'severidad' => 'alta',
                'activa' => true,
                'bloqueante' => true,
                'parametros' => [
                    'palabras_clave' => ['laboratorio', 'práctica', 'taller', 'lab']
                ],
            ],
            [
                'codigo' => 'CAPACIDAD',
                'nombre' => 'Capacidad de Aula',
                'descripcion' => 'Verifica que el cupo del grupo no exceda la capacidad del aula asignada',
                'categoria' => 'capacidad',
                'severidad' => 'alta',
                'activa' => true,
                'bloqueante' => false,
                'parametros' => ['margen_porcentaje' => 10],
            ],
            [
                'codigo' => 'CONTINUIDAD',
                'nombre' => 'Días Consecutivos de Clase',
                'descripcion' => 'Limita la cantidad de días seguidos en los que un docente debe impartir clases',
                'categoria' => 'continuidad',
                'severidad' => 'baja',
                'activa' => false,
                'bloqueante' => false,
                'parametros' => ['max_dias' => 5],
            ],
            [
                'codigo' => 'CARGA_CONTRATADA',
                'nombre' => 'Horas Contratadas',
                'descripcion' => 'Verifica que el docente no exceda su carga horaria contratada',
                'categoria' => 'carga_docente',
                'severidad' => 'alta',
                'activa' => true,
                'bloqueante' => false,
                'parametros' => null,
            ],
        ];

        foreach ($reglasDefecto as $regla) {
            ReglaValidacion::firstOrCreate(
                ['codigo' => $regla['codigo']],
                $regla
            );
        }

        $this->command->info('✓ Reglas de validación predeterminadas creadas exitosamente');
    }
}
