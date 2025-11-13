<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Genera bloques horarios dinámicamente a partir de “ventanas”
 * con pasos en minutos (90 o 135, según el tramo del día).
 *
 * Resultado típico (coincide con tu oferta):
 * - 07:00–09:15 (135)
 * - 09:15–11:30 (135)
 * - 11:30–13:00 (90)
 * - 13:45–15:15 (90)
 * - 15:15–16:45 (90)
 * - 16:45–18:15 (90)
 * - 18:15–19:45 (90)
 * - 19:45–21:15 (90)
 * - 20:30–22:45 (135)
 * + excepciones: 07:00–08:30 y 10:00–11:30
 */
class BloqueSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza (si quieres conservar historiales, comenta esta línea)
        #DB::table('bloques')->truncate();

        // Ventanas base (inicio, fin, paso_en_minutos)
        // AM largos, MD/Tarde cortos, noche corta y tarde-noche larga.
        $windows = [
            // Mañana larga: 07:00→11:30 con bloques de 135'
            ['start' => '07:00', 'end' => '11:30', 'step' => 135],
            // Mediodía corto: 11:30→13:00 con 90'
            ['start' => '11:30', 'end' => '13:00', 'step' => 90],
            // Tarde corta: 13:45→18:15 con 90'
            ['start' => '13:45', 'end' => '18:15', 'step' => 90],
            // Noche corta: 18:15→21:15 con 90'
            ['start' => '18:15', 'end' => '21:15', 'step' => 90],
            // Tarde-noche larga: 20:30→22:45 con 135' (se solapa a propósito)
            ['start' => '20:30', 'end' => '22:45', 'step' => 135],
        ];

        // Excepciones/variantes que aparecen en tu oferta (opcionales):
        $manualExtras = [
            ['inicio' => '07:00', 'fin' => '08:30'], // Sábados u ofertas especiales
            ['inicio' => '10:00', 'fin' => '11:30'],
        ];

        // Generador a partir de las ventanas
        $bloques = [];
        foreach ($windows as $w) {
            $cursor = Carbon::createFromTimeString($w['start']);
            $limit  = Carbon::createFromTimeString($w['end']);
            $step   = $w['step'];

            while ($cursor->lt($limit)) {
                $inicio = $cursor->copy();
                $fin    = $cursor->copy()->addMinutes($step);

                // Si el fin se pasa del límite, corta exactamente en el límite
                if ($fin->gt($limit)) {
                    $fin = $limit->copy();
                }

                // Evita insertar bloques nulos o ya repetidos
                if ($fin->gt($inicio)) {
                    $bloques[] = [
                        'hora_inicio' => $inicio->format('H:i'),
                        'hora_fin'    => $fin->format('H:i'),
                    ];
                }

                // Avanza al siguiente inicio
                $cursor->addMinutes($step);
            }
        }

        // Añade excepciones manuales
        foreach ($manualExtras as $e) {
            $bloques[] = [
                'hora_inicio' => Carbon::createFromTimeString($e['inicio'])->format('H:i'),
                'hora_fin'    => Carbon::createFromTimeString($e['fin'])->format('H:i'),
            ];
        }

        // Normaliza, elimina duplicados y ordena por hora_inicio, hora_fin
        $unique = collect($bloques)
            ->unique(function ($b) {
                return $b['hora_inicio'].'-'.$b['hora_fin'];
            })
            ->sortBy(['hora_inicio', 'hora_fin'])
            ->values()
            ->all();

        // Inserta con etiqueta B01, B02, ...
        $now = now();
        $toInsert = [];
        foreach ($unique as $i => $b) {
            $toInsert[] = [
                'hora_inicio' => $b['hora_inicio'],
                'hora_fin'    => $b['hora_fin'],
                'etiqueta'    => 'B'.str_pad((string)($i+1), 2, '0', STR_PAD_LEFT),
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('bloques')->insert($toInsert);
    }
}
