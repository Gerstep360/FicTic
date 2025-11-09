<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BloquesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloques = [
            // Bloques de la mañana
            [
                'etiqueta' => '1er Bloque',
                'hora_inicio' => '06:45:00',
                'hora_fin' => '08:15:00',
            ],
            [
                'etiqueta' => '2do Bloque',
                'hora_inicio' => '08:15:00',
                'hora_fin' => '09:45:00',
            ],
            [
                'etiqueta' => '3er Bloque',
                'hora_inicio' => '09:45:00',
                'hora_fin' => '11:15:00',
            ],
            [
                'etiqueta' => '4to Bloque',
                'hora_inicio' => '11:15:00',
                'hora_fin' => '12:45:00',
            ],
            // Bloques de la tarde
            [
                'etiqueta' => '5to Bloque',
                'hora_inicio' => '12:45:00',
                'hora_fin' => '14:15:00',
            ],
            [
                'etiqueta' => '6to Bloque',
                'hora_inicio' => '14:15:00',
                'hora_fin' => '15:45:00',
            ],
            [
                'etiqueta' => '7mo Bloque',
                'hora_inicio' => '15:45:00',
                'hora_fin' => '17:15:00',
            ],
            [
                'etiqueta' => '8vo Bloque',
                'hora_inicio' => '17:15:00',
                'hora_fin' => '18:45:00',
            ],
            // Bloques de la noche
            [
                'etiqueta' => '9no Bloque',
                'hora_inicio' => '18:45:00',
                'hora_fin' => '20:15:00',
            ],
            [
                'etiqueta' => '10mo Bloque',
                'hora_inicio' => '20:15:00',
                'hora_fin' => '21:45:00',
            ],
        ];

        foreach ($bloques as $bloque) {
            DB::table('bloques')->insert(array_merge($bloque, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✓ 10 bloques horarios creados exitosamente');
    }
}
