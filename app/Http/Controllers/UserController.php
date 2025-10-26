<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Support\LogsBitacora;

class UserController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // Solo quien tenga permisos de gestión de usuarios
        $this->middleware(['permission:generar_cuentas'])->except(['index', 'show']);
    }

    /**
     * Vista: listado de usuarios con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:100'],
            'role'     => ['sometimes','string','max:100'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = User::query()->with('roles:id,name');

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->where(function($query) use ($like) {
                $query->whereRaw('LOWER(name) LIKE ?', [$like])
                      ->orWhereRaw('LOWER(email) LIKE ?', [$like]);
            });
        }

        if ($roleName = $request->get('role')) {
            $q->whereHas('roles', function($query) use ($roleName) {
                $query->where('name', $roleName);
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $users   = $q->orderBy('name')->paginate($perPage)->appends($request->query());
        
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();

        return view('usuarios.index', compact('users', 'roles'));
    }

    /**
     * Vista: detalle de usuario con sus roles y permisos.
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        return view('usuarios.show', compact('user'));
    }

    /**
     * Asignar o cambiar roles a un usuario.
     */
    public function updateRoles(Request $request, User $user)
    {
        // Verificar permiso manualmente
        if (!auth()->user()->can('asignar_perfiles_ambitos') && !auth()->user()->hasRole('Admin DTIC')) {
            abort(403, 'No tienes permiso para asignar roles.');
        }

        $data = $request->validate([
            'roles'   => ['required','array'],
            'roles.*' => ['string','exists:roles,name'],
        ]);

        $antes = $user->roles->pluck('name')->toArray();

        $user->syncRoles($data['roles']);

        $despues = $user->roles()->pluck('name')->toArray();

        // Bitácora
        $this->logBitacora($request, [
            'accion'          => 'ASIGNAR_ROLES',
            'modulo'          => 'USUARIOS',
            'tabla_afectada'  => 'model_has_roles',
            'registro_id'     => $user->id,
            'descripcion'     => "Cambio de roles para usuario {$user->name}",
            'cambios_antes'   => ['roles' => $antes],
            'cambios_despues' => ['roles' => $despues],
        ]);

        return redirect()
            ->route('usuarios.show', $user)
            ->with('status', 'Roles actualizados correctamente.');
    }
}
