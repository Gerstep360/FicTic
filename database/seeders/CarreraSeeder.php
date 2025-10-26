<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrera;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        Carrera::create([
            'nombre' => 'Ingeniería en Informática',
            'id_facultad' => 1,
        ]);
        Carrera::create([
            'nombre' => 'Ingeniería en Sistemas',
            'id_facultad' => 1,
        ]);
        Carrera::create([
            'nombre' => 'Ingeniería en Redes y Telecomunicaciones',
            'id_facultad' => 1,
        ]);
        Carrera::create([
            'nombre' => 'Ingeniería en Robótica',
            'id_facultad' => 1,
        ]);
    }
}
