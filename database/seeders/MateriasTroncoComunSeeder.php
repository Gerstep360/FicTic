<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Supports\SeedMateriasHelper;

class MateriasTroncoComunSeeder extends Seeder
{
    use SeedMateriasHelper;

    public function run(): void
    {
        // Carreras a las que aplica el tronco común (multigrupo)
        $carreras = [
            $this->resolveCarreraId('INF', 'Informática'),
            $this->resolveCarreraId('SIS', 'Sistemas'),
            $this->resolveCarreraId('RED', 'Redes'),
        ];

        $m = [
            // 1er
            ['codigo'=>'FIS100','nombre'=>'FISICA I','nivel'=>1,'creditos'=>6,'req'=>[]],
            ['codigo'=>'INF110','nombre'=>'INTRODUCCION A LA INFORMATICA','nivel'=>1,'creditos'=>5,'req'=>[]],
            ['codigo'=>'INF119','nombre'=>'ESTRUCTURAS DISCRETAS','nivel'=>1,'creditos'=>5,'req'=>[]],
            ['codigo'=>'LIN100','nombre'=>'INGLES TECNICO I','nivel'=>1,'creditos'=>4,'req'=>[]],
            ['codigo'=>'MAT101','nombre'=>'CALCULO I','nivel'=>1,'creditos'=>5,'req'=>[]],

            // 2do
            ['codigo'=>'FIS102','nombre'=>'FISICA II','nivel'=>2,'creditos'=>6,'req'=>['FIS100']],
            ['codigo'=>'INF120','nombre'=>'PROGRAMACION I','nivel'=>2,'creditos'=>5,'req'=>['INF110']],
            ['codigo'=>'LIN101','nombre'=>'INGLES TECNICO II','nivel'=>2,'creditos'=>4,'req'=>['LIN100']],
            ['codigo'=>'MAT102','nombre'=>'CALCULO II','nivel'=>2,'creditos'=>5,'req'=>['MAT101']],
            ['codigo'=>'MAT103','nombre'=>'ALGEBRA LINEAL','nivel'=>2,'creditos'=>5,'req'=>['INF119']],

            // 3er
            ['codigo'=>'ADM100','nombre'=>'ADMINISTRACION','nivel'=>3,'creditos'=>4,'req'=>[]],
            ['codigo'=>'INF210','nombre'=>'PROGRAMACION II','nivel'=>3,'creditos'=>5,'req'=>['INF120','MAT103']],
            ['codigo'=>'INF211','nombre'=>'ARQUITECTURA DE COMPUTADORAS','nivel'=>3,'creditos'=>5,'req'=>['FIS102','INF120']],
            ['codigo'=>'MAT207','nombre'=>'ECUACIONES DIFERENCIALES','nivel'=>3,'creditos'=>5,'req'=>['MAT102']],
            ['codigo'=>'FIS200','nombre'=>'FISICA III','nivel'=>3,'creditos'=>6,'req'=>['FIS102']],
            // Optativas para habilitar ruta Redes
            ['codigo'=>'ELT241','nombre'=>'TEORIA DE CAMPOS','nivel'=>3,'creditos'=>4,'req'=>['FIS102']],
            ['codigo'=>'RDS210','nombre'=>'ANALISIS DE CIRCUITOS','nivel'=>3,'creditos'=>5,'req'=>['FIS102']],

            // 4to
            ['codigo'=>'ADM200','nombre'=>'CONTABILIDAD','nivel'=>4,'creditos'=>4,'req'=>['ADM100']],
            ['codigo'=>'INF220','nombre'=>'ESTRUCTURA DE DATOS I','nivel'=>4,'creditos'=>5,'req'=>['INF210','MAT101']],
            ['codigo'=>'INF221','nombre'=>'PROGRAMACION ENSAMBLADOR','nivel'=>4,'creditos'=>5,'req'=>['INF211']],
            ['codigo'=>'MAT202','nombre'=>'PROBABILIDADES Y ESTADIST. I','nivel'=>4,'creditos'=>5,'req'=>['MAT102']],
            ['codigo'=>'MAT205','nombre'=>'METODOS NUMERICOS','nivel'=>4,'creditos'=>5,'req'=>['MAT207']],
            // Optativa IV (Redes)
            ['codigo'=>'RDS220','nombre'=>'ANALISIS DE CIRCUITOS ELECTRON.','nivel'=>4,'creditos'=>4,'req'=>['RDS210']],
        ];

        foreach ($carreras as $idCarrera) {
            $this->upsertMaterias($idCarrera, $m);
        }

        $this->command?->info('✅ Tronco común insertado para INF/SIS/RED.');
    }
}
