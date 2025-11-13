<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Supports\SeedMateriasHelper;

class MateriasRedesSeeder extends Seeder
{
    use SeedMateriasHelper;

    public function run(): void
    {
        $id = $this->resolveCarreraId('RED', 'Redes');

        $m = [
            // 5to
            ['codigo'=>'ELT352','nombre'=>'SISTEMAS LOGICOS Y DIGITALES I','nivel'=>5,'creditos'=>5,'req'=>['RDS220']],
            ['codigo'=>'ELT354','nombre'=>'SEÑALES Y SISTEMAS','nivel'=>5,'creditos'=>5,'req'=>['ELT241']],
            ['codigo'=>'INF312','nombre'=>'BASE DE DATOS I','nivel'=>5,'creditos'=>5,'req'=>['INF220']],
            ['codigo'=>'MAT302','nombre'=>'PROBABILIDADES Y ESTADIST. II','nivel'=>5,'creditos'=>5,'req'=>['MAT202']],
            ['codigo'=>'RDS310','nombre'=>'ELECTRONICA APLICADA A REDES','nivel'=>5,'creditos'=>5,'req'=>['RDS220']],

            // 6to
            ['codigo'=>'ELT362','nombre'=>'SISTEMAS LOGICOS Y DIGITALES II','nivel'=>6,'creditos'=>5,'req'=>['ELT352']],
            ['codigo'=>'INF322','nombre'=>'BASES DE DATOS II','nivel'=>6,'creditos'=>5,'req'=>['INF312']],
            ['codigo'=>'INF323','nombre'=>'SISTEMAS OPERATIVOS I','nivel'=>6,'creditos'=>5,'req'=>['INF310']],
            ['codigo'=>'MAT329','nombre'=>'INVESTIGACION OPERATIVA I','nivel'=>6,'creditos'=>5,'req'=>['MAT302']],
            ['codigo'=>'RDS320','nombre'=>'INTERPRET. DE SISTEMAS Y SEÑALES','nivel'=>6,'creditos'=>5,'req'=>['ELT354','MAT207']],

            // 7mo
            ['codigo'=>'ELT374','nombre'=>'SISTEMAS DE COMUNICACION I','nivel'=>7,'creditos'=>5,'req'=>['ELT354','RDS320']],
            ['codigo'=>'INF413','nombre'=>'SISTEMAS OPERATIVOS II','nivel'=>7,'creditos'=>5,'req'=>['INF323']],
            ['codigo'=>'RDS410','nombre'=>'APLICACIONES CON MICROPROCESADORES','nivel'=>7,'creditos'=>5,'req'=>['ELT362','INF221']],
            ['codigo'=>'INF433','nombre'=>'REDES I','nivel'=>7,'creditos'=>5,'req'=>['INF323']],
            ['codigo'=>'MAT419','nombre'=>'INVESTIGACION OPERATIVA II','nivel'=>7,'creditos'=>5,'req'=>['MAT329']],

            // 8vo
            ['codigo'=>'ECO449','nombre'=>'PREP. Y EVAL. DE PROYECTOS','nivel'=>8,'creditos'=>5,'req'=>['MAT419']],
            ['codigo'=>'ELT384','nombre'=>'SISTEMAS DE COMUNICACION II','nivel'=>8,'creditos'=>5,'req'=>['ELT374']],
            ['codigo'=>'RDS421','nombre'=>'TALLER DE ANALISIS Y DISEÑO REDES','nivel'=>8,'creditos'=>4,'req'=>['INF433']],
            ['codigo'=>'RDS429','nombre'=>'LEGISLACION EN REDES Y COMUNIC.','nivel'=>8,'creditos'=>5,'req'=>['ELT374','INF433']],
            ['codigo'=>'INF423','nombre'=>'REDES II','nivel'=>8,'creditos'=>5,'req'=>['INF433']],

            // 9no
            ['codigo'=>'INF511','nombre'=>'TALLER DE GRADO I','nivel'=>9,'creditos'=>5,'req'=>['ECO449']],
            ['codigo'=>'RDS511','nombre'=>'GESTION Y ADMIN. DE REDES','nivel'=>9,'creditos'=>4,'req'=>['ELT384','INF423','RDS421','RDS429']],
            ['codigo'=>'RDS512','nombre'=>'REDES INALAMB. Y COMUN. MOVILES','nivel'=>9,'creditos'=>5,'req'=>['ELT384','INF423','RDS421','RDS429']],
            ['codigo'=>'RDS519','nombre'=>'SEGURIDAD EN REDES Y TRANSDATOS','nivel'=>9,'creditos'=>5,'req'=>['ELT384','INF423','RDS421','RDS429']],
            ['codigo'=>'INF513','nombre'=>'TECNOLOGIA WEB','nivel'=>9,'creditos'=>5,'req'=>['ECO449']],

            // 10mo + electivas
            ['codigo'=>'INF521','nombre'=>'TALLER DE GRADO II','nivel'=>10,'creditos'=>5,'req'=>['INF511']],
            ['codigo'=>'ELC107','nombre'=>'CRIPTOGRAFIA Y SEGURIDAD','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC108','nombre'=>'CONTROL Y AUTOMATIZACION','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC209','nombre'=>'TECNOLOGIAS EMERGENTES EN REDES I','nivel'=>10,'creditos'=>3,'req'=>[]],
            ['codigo'=>'ELC210','nombre'=>'TECNOLOGIAS EMERGENTES EN REDES II','nivel'=>10,'creditos'=>3,'req'=>[]],
        ];

        $this->upsertMaterias($id, $m);
        $this->command?->info('✅ Materias ruta Redes y Telecom listas.');
    }
}
