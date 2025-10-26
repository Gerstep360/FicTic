<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulasSeeder extends Seeder
{
    public function run(): void
    {
        $aulas = [
            // Módulo 236 (teóricas)
            ['codigo'=>'236-01','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-02','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-03','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-04','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-05','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-06','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-07','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],
            ['codigo'=>'236-08','tipo'=>'Teórica','capacidad'=>60,'edificio'=>'Módulo 236'],

            // Laboratorios de computación
            ['codigo'=>'LAB-INF-01','tipo'=>'Computación','capacidad'=>40,'edificio'=>'Bloque Informática'],
            ['codigo'=>'LAB-INF-02','tipo'=>'Computación','capacidad'=>40,'edificio'=>'Bloque Informática'],
            ['codigo'=>'LAB-INF-03','tipo'=>'Computación','capacidad'=>35,'edificio'=>'Bloque Informática'],
            ['codigo'=>'LAB-REDES','tipo'=>'Laboratorio','capacidad'=>28,'edificio'=>'Bloque Informática'],

            // Otros
            ['codigo'=>'AUD-ING-1','tipo'=>'Auditorio','capacidad'=>120,'edificio'=>'Edificio Ingeniería'],
            ['codigo'=>'AULA-MULTI-01','tipo'=>'Teórica','capacidad'=>80,'edificio'=>'Módulo Multidisciplinario'],
        ];

        foreach ($aulas as $a) {
            Aula::updateOrCreate(['codigo' => $a['codigo']], $a);
        }

        $this->command?->info('✅ AulasSeeder: catálogo base de aulas insertado/actualizado.');
    }
}
