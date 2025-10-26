<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriasSeeder extends Seeder
{
    public function run(): void
    {
        $d = fn($codigo, $nombre, $creditos = 5, $idCarrera = 1) =>
            ['codigo'=>$codigo,'nombre'=>$nombre,'nivel'=>'Licenciatura','creditos'=>$creditos,'id_carrera'=>$idCarrera];

        $materias = [

            // 1er semestre
            $d('FIS100','Física I'),
            $d('INF110','Introducción a la Informática'),
            $d('INF119','Estructuras Discretas'),
            $d('LIN100','Inglés Técnico I'),
            $d('MAT101','Cálculo I'),

            // 2do semestre
            $d('FIS102','Física II'),
            $d('INF120','Programación I'),
            $d('LIN101','Inglés Técnico II',4),
            $d('MAT102','Cálculo II'),
            $d('MAT103','Álgebra Lineal'),

            // 3er semestre
            $d('ADM100','Administración General',4),
            $d('FIS200','Física III'),
            $d('INF210','Programación II'),
            $d('INF211','Arquitectura de Computadoras'),
            $d('NAT207','Ecuaciones Diferenciales'), // aparece así en tu maestro
            $d('MAT207','Ecuaciones Diferenciales'),

            // 4to semestre
            $d('ADM200','Contabilidad',4),
            $d('INF220','Estructura de Datos I'),
            $d('INF221','Programación Ensamblador'),
            $d('MAT202','Probabilidades y Estadística I'),
            $d('MAT205','Métodos Numéricos'),
            $d('ELC102','Programación Gráfica'),

            // 5to semestre
            $d('INF310','Estructuras de Datos II'),
            $d('INF312','Bases de Datos I'),
            $d('INF318','Programación Lógica y Funcional'),
            $d('INF319','Lenguajes Formales'),
            $d('MAT302','Probabilidades y Estadística II'),
            $d('ADM330','Organización y Métodos'),
            $d('ECO300','Economía para la Gestión'),

            // 6to semestre
            $d('ELC103','Tópicos Avanzados de Programación'),
            $d('INF322','Bases de Datos II'),
            $d('INF329','Compiladores'),
            $d('INF323','Sistemas Operativos I'),
            $d('INF342','Sistemas de Información I'),
            $d('MAT329','Investigación Operativa I'),
            $d('ELC005','Ingeniería de la Calidad'),
            $d('ADM320','Finanzas para la Empresa'),

            // 7mo semestre
            $d('INF412','Sistemas de Información II'),
            $d('INF413','Sistemas Operativos II'),
            $d('INF418','Inteligencia Artificial'),
            $d('INF432','Sistemas de Soporte a Decisiones'),
            $d('INF433','Redes I'),
            $d('MAT419','Investigación Operativa II'),
            $d('ELC106','Interacción Hombre-Computador'),

            // 8vo semestre
            $d('ECO449','Preparación y Evaluación de Proyectos'),
            $d('ELC008','Legislación en Ciencias de la Computación'),
            $d('ELC107','Criptografía y Seguridad'),
            $d('INF422','Ingeniería de Software I'),
            $d('INF423','Redes II'),
            $d('INF428','Sistemas Expertos'),
            $d('INF442','Sistemas de Información Geográfica'),
            $d('INF462','Auditoría Informática'),

            // 9no semestre
            $d('INF511','Taller de Grado I',6),
            $d('INF512','Ingeniería de Software II',4),
            $d('INF513','Tecnología Web',4),
            $d('INF552','Proyecto de Grado II',6),

            // Redes / Electrónica / Telecom (extras que salen en tu oferta)
            $d('RDS210','Análisis de Circuitos'),
            $d('RDS220','Análisis de Circuitos Electrónicos'),
            $d('RDS320','Interpretación de Sistemas y Señal'),
            $d('RDS421','Taller de Análisis y Diseño de Redes'),
            $d('RDS429','Legislación en Redes y Comunicaciones'),
            $d('RDS511','Gestión y Administración de Redes'),
            $d('RDS512','Redes Inalámbricas y Comunicaciones Móviles'),
            $d('RDS410','Aplicaciones con Microprocesadores'),

            $d('ELT241','Teoría de Campos'),
            $d('ELT352','Sistemas Lógicos y Digitales I'),
            $d('ELT354','Señales y Sistemas'),
            $d('ELT362','Sistemas Lógicos y Digitales II'),
            $d('ELT374','Sistemas de Comunicación I'),
            $d('ELT384','Sistemas de Comunicación II'),

            $d('ELC201','Diseño de Circuitos Integrados'),
            $d('ELC203','Sistemas de Comunicación SCADA'),
            $d('ELC204','Televisión Digital'),
            $d('ELC206','Líneas de Transmisión y Antenas'),
            $d('ELC208','Redes AdHoc'),

            // Otros de tu maestro (CI/W1/optativas)
            $d('MET100','Metodología de la Investigación',4),
            $d('ROB101','Introducción a la Robótica',4),
            $d('ROB102','Dibujo Mecánico en CAD',4),

            // Alias/normalizaciones que aparecían con typo en tu fuente:
            $d('INF111','Arquitectura de Computadoras'),   // si tu plan lo usa además de INF211
            $d('ELC105','Sistemas Distribuidos'),           // “ELC1D5” en tu texto
            $d('ELC002','Costos y Presupuestos',4),         // visto en tu maestro 2024-2
            $d('ELC003','Producción y Marketing',4),
        ];

        foreach ($materias as $m) {
            Materia::updateOrCreate(['codigo' => $m['codigo']], $m);
        }
    }
}
