<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facultad;

class FacultadSeeder extends Seeder
{
    public function run(): void
    {
        Facultad::create([
            'nombre' => 'Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones',
        ]);
    }
}
