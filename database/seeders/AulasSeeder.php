<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulasSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia si lo deseas (comenta si no)
        // Aula::query()->delete();

        /**
         * Definición por plantas del Módulo 236.
         * Cada planta tiene un rango de códigos y metadatos por defecto.
         * Puedes afinar capacidades por piso o por aula puntual en overrides.
         */
        $plantas = [
            // Planta 10: 23611–23617
            [
                'rango'     => [23611, 23617],
                'tipo'      => 'Teórica',
                'capacidad' => 60,
                'edificio'  => 'Módulo 236',
            ],
            // Planta 20: 23621–23627
            [
                'rango'     => [23621, 23627],
                'tipo'      => 'Teórica',
                'capacidad' => 60,
                'edificio'  => 'Módulo 236',
            ],
            // Planta 30: 23631–23637
            [
                'rango'     => [23631, 23637],
                'tipo'      => 'Teórica',
                'capacidad' => 60,
                'edificio'  => 'Módulo 236',
            ],
            // Planta 40 (Labs funcionales): 23641–23646
            [
                'rango'     => [23641, 23646],
                'tipo'      => 'Laboratorio',
                'capacidad' => 36, // promedio; puedes ajustar con overrides
                'edificio'  => 'Módulo 236 (Laboratorios)',
            ],
        ];

        /**
         * Overrides por aula puntual.
         * - 23640: auditorio que también se usa para clases/exámenes.
         * - Capacidades específicas por laboratorio (si quieres afinarlas aquí).
         */
        $overrides = [
            23640 => ['tipo' => 'Auditorio', 'capacidad' => 120, 'edificio' => 'Módulo 236 (Auditorio)'],

            // Afinar laboratorios (opcional):
            23641 => ['capacidad' => 40],
            23642 => ['capacidad' => 40],
            23643 => ['capacidad' => 45],
            23644 => ['capacidad' => 35],
            23645 => ['capacidad' => 28],
            23646 => ['capacidad' => 32],
        ];

        // Construye el catálogo desde las plantas
        $aulas = [];

        foreach ($plantas as $planta) {
            [$ini, $fin] = $planta['rango'];

            for ($code = $ini; $code <= $fin; $code++) {
                $base = [
                    'codigo'    => (string) $code,
                    'tipo'      => $planta['tipo'],
                    'capacidad' => $planta['capacidad'],
                    'edificio'  => $planta['edificio'],
                ];

                // Aplica override si existe
                if (array_key_exists($code, $overrides)) {
                    $base = array_merge($base, $overrides[$code]);
                }

                $aulas[] = $base;
            }
        }

        // Aulas sueltas fuera de rango que quieras incluir (ej.: auditorio ya cubierto)
        // $aulas[] = ['codigo' => 'AUD-ING-1', 'tipo' => 'Auditorio', 'capacidad' => 120, 'edificio' => 'Edificio Ingeniería'];

        // Inserta/actualiza
        foreach ($aulas as $a) {
            Aula::updateOrCreate(['codigo' => $a['codigo']], $a);
        }

        $this->command?->info('✅ AulasSeeder: aulas del Módulo 236 (4 plantas) insertadas/actualizadas.');
    }
}
