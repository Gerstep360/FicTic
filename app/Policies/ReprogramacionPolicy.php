<?php

namespace App\Policies;

use App\Models\Reprogramacion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReprogramacionPolicy
{
    use HandlesAuthorization;

    /**
     * Ver cualquier reprogramación
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gestionar_reprogramaciones');
    }

    /**
     * Ver reprogramación específica
     */
    public function view(User $user, Reprogramacion $reprogramacion): bool
    {
        // El solicitante puede ver su propia reprogramación
        if ($user->id === $reprogramacion->solicitado_por) {
            return true;
        }

        // Coordinadores y directores pueden ver todas
        return $user->hasPermissionTo('gestionar_reprogramaciones');
    }

    /**
     * Crear reprogramación
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('gestionar_reprogramaciones');
    }

    /**
     * Aprobar reprogramación
     */
    public function aprobar(User $user, Reprogramacion $reprogramacion): bool
    {
        // Solo coordinadores/directores y debe estar pendiente
        return $user->hasPermissionTo('aprobar_reprogramaciones') 
               && $reprogramacion->estado === 'PENDIENTE';
    }

    /**
     * Rechazar reprogramación
     */
    public function rechazar(User $user, Reprogramacion $reprogramacion): bool
    {
        // Mismo criterio que aprobar
        return $user->hasPermissionTo('aprobar_reprogramaciones') 
               && $reprogramacion->estado === 'PENDIENTE';
    }

    /**
     * Eliminar reprogramación
     */
    public function delete(User $user, Reprogramacion $reprogramacion): bool
    {
        // Solo si está pendiente y es el solicitante o tiene permiso de gestión
        if ($reprogramacion->estado !== 'PENDIENTE') {
            return false;
        }

        return $user->id === $reprogramacion->solicitado_por 
               || $user->hasPermissionTo('gestionar_reprogramaciones');
    }
}
