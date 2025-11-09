<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        /**
         * Permisos mapeados a los Casos de Uso del documento
         * Fase 0 – Preparación institucional
         *   CU-01 abrir_gestion
         *   CU-02 registrar_unidades_academicas
         *   CU-03 definir_roles_perfiles
         *   CU-04 configurar_catalogos
         *
         * Fase 1 – Gestión de Usuarios y Seguridad
         *   CU-05 importar_usuarios
         *   CU-06 generar_cuentas
         *   CU-07 asignar_perfiles_ambitos
         *   (CU-08 y CU-09 son de autenticación/recuperación; no se modelan como permisos)
         *
         * Fase 2 – Oferta Académica y Recursos
         *   CU-10 gestionar_asignaturas
         *   CU-11 gestionar_grupos
         *   CU-12 gestionar_aulas
         *   CU-13 registrar_carga_docente
         *
         * Fase 3 – Programación de Horarios
         *   CU-14 asignar_horarios
         *   CU-15 generar_horario_auto
         *   CU-16 validar_conflictos
         *   CU-17 aprobar_horarios
         *   CU-18 publicar_horarios
         *
         * Fase 4 – Control de Asistencia Docente
         *   CU-19 generar_qr_docente
         *   CU-20 registrar_asistencia_qr
         *   CU-21 asistencia_manual
         *   CU-22 gestionar_justificaciones
         *   CU-22 gestionar_suplencias
         *
         * Fase 5 – Operación y Reportes
         *   CU-23 gestionar_reprogramaciones, aprobar_reprogramaciones
         *   CU-24 ver_reportes
         *
         * Permisos de lectura/acciones propias (docente) y consultas operativas:
         *   ver_horario_propio, ver_asistencias_propias, solicitar_justificacion, ver_horario_por_aula
         */
        $permissions = [
            // Fase 0
            'abrir_gestion',
            'registrar_unidades_academicas',
            'definir_roles_perfiles',
            'configurar_catalogos',

            // Fase 1
            'importar_usuarios',
            'generar_cuentas',
            'asignar_perfiles_ambitos',

            // Fase 2
            'gestionar_asignaturas',
            'ver_materias',
            'crear_materias',
            'editar_materias',
            'eliminar_materias',
            'restaurar_materias',
            'ver_grupos',
            'crear_grupos',
            'editar_grupos',
            'eliminar_grupos','gestionar_grupos',
            'ver_aulas',
            'crear_aulas',
            'editar_aulas',
            'eliminar_aulas',
            'restaurar_aulas',
            'gestionar_aulas',
            'registrar_carga_docente',

            // Fase 3
            'asignar_horarios',
            'generar_horario_auto',
            'validar_conflictos',
            'aprobar_horarios',
            'publicar_horarios',
            
            // Fase 4
            'generar_qr_docente',
            'registrar_asistencia_qr',
            'asistencia_manual',
            'gestionar_justificaciones',
            'solicitar_justificacion',
            'gestionar_suplencias',
            
            // Fase 5
            'gestionar_reprogramaciones',
            'aprobar_reprogramaciones',
            'ver_reportes',

            // Lectura/ámbito propio y consultas operativas
            'ver_horario_propio',
            'ver_asistencias_propias',
            'solicitar_justificacion',
            'ver_horario_por_aula',

            //ambito
            'ver_asignaciones_ambito',
            'eliminar_asignacion_ambito',

            //bitacora
            'ver_bitacora'

        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, $guard);
        }

        // Roles (actores del documento)
        $roles = [
            'Admin DTIC',
            'Decano',
            'Director',
            'Coordinador',
            'Docente',
            'Bedel',
        ];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, $guard);
        }

        // Asignación de permisos por rol, según responsabilidades del documento

        // 1) Admin DTIC: configuración global, usuarios, catálogos, programación y control (full access)
        Role::findByName('Admin DTIC', $guard)->syncPermissions($permissions);

        // 2) Decano/Vicedecano: supervisión y aprobación/publicación + reportes (visión global)
        Role::findByName('Decano', $guard)->syncPermissions([
            'abrir_gestion',            // CU-01 (puede participar en apertura)
            'publicar_horarios',        // CU-18 (aprobación final facultativa)
            'ver_reportes',             // CU-24 (reportes consolidados)
        ]);

        // 3) Director de Carrera: valida/aprueba horarios de su carrera; oferta y carga; reportes; puede asignar coordinadores (CU-07) en su ámbito
        Role::findByName('Director', $guard)->syncPermissions([
            'gestionar_asignaturas',    // CU-10
            'gestionar_grupos',         // CU-11
            'registrar_carga_docente',  // CU-13
            'asignar_horarios',         // CU-14
            'generar_horario_auto',     // CU-15
            'validar_conflictos',       // CU-16
            'aprobar_horarios',         // CU-17 (aprobación nivel carrera)
            'asignar_perfiles_ambitos', // CU-07 (limitado a su carrera por lógica de negocio)
            'gestionar_reprogramaciones', // CU-23
            'aprobar_reprogramaciones',   // CU-23
            'ver_reportes',             // CU-24
        ]);

        // 4) Coordinador de Carrera: núcleo operativo de programación y asistencia (no publica ni aprueba final)
        Role::findByName('Coordinador', $guard)->syncPermissions([
            'gestionar_asignaturas',    // CU-10
            'gestionar_grupos',         // CU-11
            'gestionar_aulas',          // CU-12 (en la práctica puede actualizar catálogos de aulas de su ámbito)
            'registrar_carga_docente',  // CU-13
            'asignar_horarios',         // CU-14
            'generar_horario_auto',     // CU-15
            'validar_conflictos',       // CU-16
            'generar_qr_docente',       // CU-19 (genera QR para docentes de su carrera)
            'registrar_asistencia_qr',  // CU-20
            'asistencia_manual',        // CU-21
            'gestionar_justificaciones',// CU-22
            'gestionar_suplencias',     // CU-22
            'gestionar_reprogramaciones', // CU-23
            'aprobar_reprogramaciones',   // CU-23
            'ver_reportes',             // CU-24
        ]);
        
        // 5) Docente: acceso limitado a su información personal
        Role::findByName('Docente', $guard)->syncPermissions([
            'ver_horario_propio',
            'ver_asistencias_propias',
            'solicitar_justificacion',
            'ver_horario_por_aula',
        ]);
        
        // 6) Bedel: control operativo de asistencia
        Role::findByName('Bedel', $guard)->syncPermissions([
            'registrar_asistencia_qr',  // CU-20 (escaneo de QR)
            'asistencia_manual',        // CU-21 (registro manual)
            'ver_horario_por_aula',     // Consulta de horarios
        ]);


        // Limpia caché nuevamente
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
