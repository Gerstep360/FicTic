<?php

namespace App\Policies;

use App\Models\Justificacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JustificacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gestionar_justificaciones');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Justificacion $justificacion): bool
    {
        // El docente puede ver sus propias justificaciones
        if ($user->id === $justificacion->id_docente) {
            return true;
        }
        
        // Coordinadores/Directores pueden ver todas
        return $user->hasPermissionTo('gestionar_justificaciones');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Docentes pueden solicitar justificaciones
        return $user->hasPermissionTo('solicitar_justificacion') 
            || $user->hasRole('Docente');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Justificacion $justificacion): bool
    {
        // Solo el docente puede editar su justificación PENDIENTE
        return $user->id === $justificacion->id_docente 
            && $justificacion->estado === 'PENDIENTE';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Justificacion $justificacion): bool
    {
        // Solo el docente puede eliminar su justificación PENDIENTE
        return $user->id === $justificacion->id_docente 
            && $justificacion->estado === 'PENDIENTE';
    }
    
    /**
     * Aprobar o rechazar justificación
     */
    public function resolver(User $user, Justificacion $justificacion): bool
    {
        // Solo coordinadores/directores pueden resolver
        return $user->hasPermissionTo('gestionar_justificaciones')
            && $justificacion->estado === 'PENDIENTE';
    }
    
    /**
     * Alias de resolver para el método aprobar
     */
    public function aprobar(User $user, Justificacion $justificacion): bool
    {
        return $this->resolver($user, $justificacion);
    }
    
    /**
     * Alias de resolver para el método rechazar
     */
    public function rechazar(User $user, Justificacion $justificacion): bool
    {
        return $this->resolver($user, $justificacion);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Justificacion $justificacion): bool
    {
        return $user->hasPermissionTo('gestionar_justificaciones');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Justificacion $justificacion): bool
    {
        return $user->hasRole('Admin DTIC');
    }
}
