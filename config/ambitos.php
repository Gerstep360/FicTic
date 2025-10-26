<?php

return [

    // Nombres posibles del súper-admin técnico
    'admin_role_names' => ['Admin DTIC', 'Administrador DTIC'],

    // Orden jerárquico (de menor a mayor). La posición define la jerarquía.
    'order' => [
        'Docente',
        'Bedel',
        'Coordinador',
        'Director',
        'Vicedecano',
        'Decano',
        'Admin DTIC',
    ],

    // Qué ámbitos acepta cada rol (por nombre). Usa alias: user|carrera|facultad.
    // Si un rol no está listado, se usa el fallback de 'defaults.allowed_scopes'.
    'allowed_scopes' => [
        'Docente'      => ['user'],        // propio
        'Coordinador'  => ['carrera'],
        'Director'     => ['carrera'],
        'Decano'       => ['facultad'],
        'Vicedecano'   => ['facultad'],
        'Admin DTIC'   => [],              // sin ámbito (rol global)
        // 'Bedel' => ['carrera'],          // si quieres limitar Bedel, descomenta
    ],

    'defaults' => [
        // Solo mostrar/permitir roles que YA tiene el usuario objetivo.
        'limit_to_existing_roles' => true,

        // Solo permitir asignar ámbito al/los roles de mayor jerarquía que ya tiene el usuario.
        'enforce_top_role' => true,

        // Si un rol no aparece en 'allowed_scopes', usamos este fallback:
        'allowed_scopes' => ['facultad', 'carrera', 'user'],
    ],
];
