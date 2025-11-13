<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocenteExterno;

class DocenteExternoSeeder extends Seeder
{
    public function run(): void
    {
        $docentes = [
            [
                'nombre_completo' => 'Dr. Carlos Méndez',
                'especialidad' => 'Inteligencia Artificial',
                'telefono' => '+591 70123456',
                'email' => 'carlos.mendez@external.com',
                'observaciones' => 'Docente invitado con experiencia en IA y Machine Learning',
                'activo' => true,
            ],
            [
                'nombre_completo' => 'Ing. María Flores',
                'especialidad' => 'Desarrollo Web',
                'telefono' => '+591 71234567',
                'email' => 'maria.flores@external.com',
                'observaciones' => 'Especialista en frameworks modernos',
                'activo' => true,
            ],
            [
                'nombre_completo' => 'Lic. Pedro Gutiérrez',
                'especialidad' => 'Base de Datos',
                'telefono' => '+591 72345678',
                'email' => 'pedro.gutierrez@external.com',
                'activo' => true,
            ],
        ];

        foreach ($docentes as $docente) {
            DocenteExterno::create($docente);
        }

        $this->command->info('✅ Docentes externos creados correctamente');
    }
}
