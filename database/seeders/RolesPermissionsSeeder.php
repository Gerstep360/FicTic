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
         *   CU-23 reprogramaciones
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

            // Lectura/ámbito propio y consultas operativas
            'ver_horario_propio',
            'ver_asistencias_propias',
            'solicitar_justificacion',
            'ver_horario_por_aula',

            //ambito
            'asignar_perfiles_ambitos',
            'ver_asignaciones_ambito',
            'eliminar_asignacion_ambito',

            //bitacora
            'ver_bitacora',
            'ver_reportes'

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
        ]);

        // 3) Director de Carrera: valida/aprueba horarios de su carrera; oferta y carga; reportes; puede asignar coordinadores (CU-07) en su ámbito
        Role::findByName('Director', $guard)->syncPermissions([
            'gestionar_asignaturas',    // CU-10
            'gestionar_grupos',         // CU-11
            'asignar_perfiles_ambitos', // CU-07 (limitado a su carrera por lógica de negocio)
        ]);

        // 4) Coordinador de Carrera: núcleo operativo de programación y asistencia (no publica ni aprueba final)
        Role::findByName('Coordinador', $guard)->syncPermissions([
            'gestionar_asignaturas',    // CU-10
            'gestionar_grupos',         // CU-11
            'gestionar_aulas',          // CU-12 (en la práctica puede actualizar catálogos de aulas de su ámbito)
        ]);


        // Limpia caché nuevamente
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
