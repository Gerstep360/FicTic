<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Supports\SeedMateriasHelper;

class MateriasSistemasSeeder extends Seeder
{
    use SeedMateriasHelper;

    public function run(): void
    {
        $id = $this->resolveCarreraId('SIS', 'Sistemas');

        $m = [
            // 5to
            ['codigo'=>'INF310','nombre'=>'ESTRUCTURAS DE DATOS II','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'INF312','nombre'=>'BASE DE DATOS I','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'ADM330','nombre'=>'ORGANIZACION Y METODOS','nivel'=>5,'creditos'=>5,'req'=>['ADM200']],
            ['codigo'=>'ECO300','nombre'=>'ECONOMIA PARA LA GESTION','nivel'=>5,'creditos'=>5,'req'=>[]],
            ['codigo'=>'MAT302','nombre'=>'PROBABILIDADES Y ESTADIST. II','nivel'=>5,'creditos'=>5,'req'=>['MAT202']],

            // 6to
            ['codigo'=>'INF322','nombre'=>'BASES DE DATOS II','nivel'=>6,'creditos'=>5,'req'=>['INF312']],
            ['codigo'=>'INF323','nombre'=>'SISTEMAS OPERATIVOS I','nivel'=>6,'creditos'=>5,'req'=>['INF310']],
            ['codigo'=>'INF342','nombre'=>'SISTEMAS DE INFORMACION I','nivel'=>6,'creditos'=>5,'req'=>['INF312']],
            ['codigo'=>'ADM320','nombre'=>'FINANZAS PARA LA EMPRESA','nivel'=>6,'creditos'=>5,'req'=>['ADM330']],
            ['codigo'=>'MAT329','nombre'=>'INVESTIGACION OPERATIVA I','nivel'=>6,'creditos'=>5,'req'=>['MAT302']],

            // 7mo
            ['codigo'=>'INF412','nombre'=>'SISTEMAS DE INFORMACION II','nivel'=>7,'creditos'=>5,'req'=>['INF322','INF342']],
            ['codigo'=>'INF432','nombre'=>'SIST. P/ SOPORTE A LA TOMA DEC.','nivel'=>7,'creditos'=>5,'req'=>['INF322']],
            ['codigo'=>'INF413','nombre'=>'SISTEMAS OPERATIVOS II','nivel'=>7,'creditos'=>5,'req'=>['INF323']],
            ['codigo'=>'INF433','nombre'=>'REDES I','nivel'=>7,'creditos'=>5,'req'=>['INF323']],
            ['codigo'=>'MAT419','nombre'=>'INVESTIGACION OPERATIVA II','nivel'=>7,'creditos'=>5,'req'=>['MAT329']],

            // 8vo
            ['codigo'=>'ECO449','nombre'=>'PREP. Y EVAL. DE PROYECTOS','nivel'=>8,'creditos'=>5,'req'=>['MAT419']],
            ['codigo'=>'INF422','nombre'=>'INGENIERIA DE SOFTWARE I','nivel'=>8,'creditos'=>5,'req'=>['INF412']],
            ['codigo'=>'INF462','nombre'=>'AUDITORIA INFORMATICA','nivel'=>8,'creditos'=>4,'req'=>['ADM320','INF412']],
            ['codigo'=>'INF442','nombre'=>'SISTEMAS DE INFORM. GEOGRAFICA','nivel'=>8,'creditos'=>4,'req'=>['INF412']],
            ['codigo'=>'ELC105','nombre'=>'SISTEMAS DISTRIBUIDOS','nivel'=>8,'creditos'=>3,'req'=>[]],

            // 9no
            ['codigo'=>'INF511','nombre'=>'TALLER DE GRADO I','nivel'=>9,'creditos'=>5,'req'=>['ECO449','INF422']],
            ['codigo'=>'INF512','nombre'=>'INGENIERIA DE SOFTWARE II','nivel'=>9,'creditos'=>5,'req'=>['ECO449','INF422']],
            ['codigo'=>'INF513','nombre'=>'TECNOLOGIA WEB','nivel'=>9,'creditos'=>5,'req'=>['ECO449','INF422']],
            ['codigo'=>'INF552','nombre'=>'ARQUITECTURA DEL SOFTWARE','nivel'=>9,'creditos'=>4,'req'=>['ECO449','INF422']],
            ['codigo'=>'ELC107','nombre'=>'CRIPTOGRAFIA Y SEGURIDAD','nivel'=>9,'creditos'=>3,'req'=>[]],

            // 10mo
            ['codigo'=>'INF521','nombre'=>'TALLER DE GRADO II','nivel'=>10,'creditos'=>5,'req'=>['INF511','INF512','INF513','INF552']],
            ['codigo'=>'ELC106','nombre'=>'INTERACCION HOMBRE-COMPUTADOR','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC101','nombre'=>'MODELADO Y SIMULACION DE SISTEMAS','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC102','nombre'=>'PROGRAMACION GRAFICA','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC108','nombre'=>'CONTROL Y AUTOMATIZACION','nivel'=>10,'creditos'=>3,'req'=>[]],
        ];

        $this->upsertMaterias($id, $m);
        $this->command?->info('✅ Materias ruta Ingeniería de Sistemas listas.');
    }
}
