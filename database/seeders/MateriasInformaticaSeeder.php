<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Supports\SeedMateriasHelper;

class MateriasInformaticaSeeder extends Seeder
{
    use SeedMateriasHelper;

    public function run(): void
    {
        $id = $this->resolveCarreraId('INF', 'Informática');

        $m = [
            // 5to
            ['codigo'=>'INF310','nombre'=>'ESTRUCTURAS DE DATOS II','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'INF312','nombre'=>'BASE DE DATOS I','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'INF318','nombre'=>'PROGRAMACION LOGICA Y FUNCIONAL','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'INF319','nombre'=>'LENGUAJES FORMALES','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'MAT302','nombre'=>'PROBABILIDADES Y ESTADIST. II','nivel'=>5,'creditos'=>5,'req'=>['MAT202']],

            // 6to
            ['codigo'=>'INF322','nombre'=>'BASES DE DATOS II','nivel'=>6,'creditos'=>5,'req'=>['INF312']],
            ['codigo'=>'INF323','nombre'=>'SISTEMAS OPERATIVOS I','nivel'=>6,'creditos'=>5,'req'=>['INF310']],
            ['codigo'=>'INF329','nombre'=>'COMPILADORES','nivel'=>6,'creditos'=>5,'req'=>['INF310','INF319']],
            ['codigo'=>'INF342','nombre'=>'SISTEMAS DE INFORMACION I','nivel'=>6,'creditos'=>5,'req'=>['INF312']],
            ['codigo'=>'MAT329','nombre'=>'INVESTIGACION OPERATIVA I','nivel'=>6,'creditos'=>5,'req'=>['MAT302']],

            // 7mo
            ['codigo'=>'INF412','nombre'=>'SISTEMAS DE INFORMACION II','nivel'=>7,'creditos'=>5,'req'=>['INF322','INF342']],
            ['codigo'=>'INF413','nombre'=>'SISTEMAS OPERATIVOS II','nivel'=>7,'creditos'=>5,'req'=>['INF323']],
            ['codigo'=>'INF418','nombre'=>'INTELIGENCIA ARTIFICIAL','nivel'=>7,'creditos'=>5,'req'=>['INF310','INF318']],
            ['codigo'=>'INF433','nombre'=>'REDES I','nivel'=>7,'creditos'=>5,'req'=>['INF323']],
            ['codigo'=>'MAT419','nombre'=>'INVESTIGACION OPERATIVA II','nivel'=>7,'creditos'=>5,'req'=>['MAT329']],

            // 8vo
            ['codigo'=>'ECO449','nombre'=>'PREP. Y EVAL. DE PROYECTOS','nivel'=>8,'creditos'=>5,'req'=>['MAT419']],
            ['codigo'=>'INF422','nombre'=>'INGENIERIA DE SOFTWARE I','nivel'=>8,'creditos'=>5,'req'=>['INF412']],
            ['codigo'=>'INF423','nombre'=>'REDES II','nivel'=>8,'creditos'=>5,'req'=>['INF433']],
            ['codigo'=>'INF428','nombre'=>'SISTEMAS EXPERTOS','nivel'=>8,'creditos'=>5,'req'=>['INF412','INF418']],
            ['codigo'=>'INF442','nombre'=>'SISTEMAS DE INFORM. GEOGRAFICA','nivel'=>8,'creditos'=>4,'req'=>['INF412']],

            // 9no + electiva
            ['codigo'=>'INF511','nombre'=>'TALLER DE GRADO I','nivel'=>9,'creditos'=>5,'req'=>['ECO449','INF422','INF423','INF428','INF442']],
            ['codigo'=>'INF512','nombre'=>'INGENIERIA DE SOFTWARE II','nivel'=>9,'creditos'=>5,'req'=>['ECO449','INF422','INF423','INF428','INF442']],
            ['codigo'=>'INF513','nombre'=>'TECNOLOGIA WEB','nivel'=>9,'creditos'=>5,'req'=>['ECO449','INF422','INF423','INF428','INF442']],
            ['codigo'=>'INF552','nombre'=>'ARQUITECTURA DEL SOFTWARE','nivel'=>9,'creditos'=>4,'req'=>['ECO449','INF422','INF423','INF428','INF442']],
            ['codigo'=>'ELC105','nombre'=>'SISTEMAS DISTRIBUIDOS','nivel'=>9,'creditos'=>3,'req'=>[]], // electiva

            // 10mo (electivas)
            ['codigo'=>'INF521','nombre'=>'TALLER DE GRADO II','nivel'=>10,'creditos'=>5,'req'=>['INF511','INF512','INF513','INF552']],
            ['codigo'=>'ELC107','nombre'=>'CRIPTOGRAFIA Y SEGURIDAD','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC106','nombre'=>'INTERACCION HOMBRE-COMPUTADOR','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC101','nombre'=>'MODELADO Y SIMULACION DE SISTEMAS','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC102','nombre'=>'PROGRAMACION GRAFICA','nivel'=>10,'creditos'=>3,'req'=>[]],
        ];

        $this->upsertMaterias($id, $m);
        $this->command?->info('✅ Materias ruta Ingeniería Informática listas.');
    }
}
