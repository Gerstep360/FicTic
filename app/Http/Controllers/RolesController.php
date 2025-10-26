<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Support\LogsBitacora;

class RolesController extends Controller
{
    use LogsBitacora;

    /** Roles “núcleo” que no debería poder eliminarse. */
    private const PROTECTED_ROLE_NAMES = [
        'Admin DTIC','Decano','Director','Coordinador','Docente','Bedel',
    ];

    public function __construct()
    {
        $this->middleware(['auth']);
        // Solo quien tenga CU-03 puede gestionar roles/permisos
        $this->middleware(['permission:definir_roles_perfiles'])->except(['index', 'show']);
    }

    /**
     * Vista: listado de roles con conteo de permisos.
     * Filtros: q (por nombre), per_page.
     */
    public function index(Request $request)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:100'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $guard = config('auth.defaults.guard', 'web');

        $q = Role::query()
            ->where('guard_name', $guard)
            ->withCount(['permissions'])
            ->orderBy('name');

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->whereRaw('LOWER(name) LIKE ?', [$like]);
        }

        $perPage = (int) $request->get('per_page', 20);
        $roles   = $q->paginate($perPage)->appends($request->query());

        return view('roles.index', compact('roles'));
    }

    /** Vista: crear rol. */
    public function create()
    {
        $guard       = config('auth.defaults.guard', 'web');
        $permissions = Permission::where('guard_name', $guard)->orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Crear rol + asignar permisos (CU-03).
     */
    public function store(Request $request)
    {
        $guard = config('auth.defaults.guard', 'web');

        $data = $request->validate([
            'name'        => [
                'required','string','max:255',
                Rule::unique('roles','name')->where(fn($q) => $q->where('guard_name',$guard)),
            ],
            'permissions' => ['sometimes','array'],
            'permissions.*' => ['integer','exists:permissions,id'],
        ]);

        DB::transaction(function () use ($data, $request, $guard) {
            $role = Role::create([
                'name'       => $data['name'],
                'guard_name' => $guard,
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
            }

            // Bitácora
            $this->logBitacora($request, [
                'accion'         => 'CREAR_ROL',
                'modulo'         => 'ROLES_PERMISOS',
                'tabla_afectada' => 'roles',
                'registro_id'    => $role->id,
                'descripcion'    => "Creación de rol {$role->name}",
                'metadata'       => [
                    'permissions' => $data['permissions'] ?? [],
                ],
            ]);
        });

        return redirect()
            ->route('roles.index')
            ->with('status', 'Rol creado correctamente.');
    }

    /** Vista: detalle de rol (con permisos). */
    public function show(Role $role)
    {
        $this->ensureGuard($role);
        $role->load('permissions:id,name,guard_name');
        return view('roles.show', compact('role'));
    }

    /** Vista: editar rol (nombre + permisos). */
    public function edit(Role $role)
    {
        $this->ensureGuard($role);

        $guard       = config('auth.defaults.guard', 'web');
        $permissions = Permission::where('guard_name',$guard)->orderBy('name')->get();
        $role->load('permissions:id');

        return view('roles.edit', compact('role','permissions'));
    }

    /**
     * Actualizar rol (nombre y/o permisos).
     */
    public function update(Request $request, Role $role)
    {
        $this->ensureGuard($role);

        $guard = config('auth.defaults.guard', 'web');

        $data = $request->validate([
            'name'        => [
                'sometimes','string','max:255',
                Rule::unique('roles','name')
                    ->where(fn($q) => $q->where('guard_name',$guard))
                    ->ignore($role->id),
            ],
            'permissions'   => ['sometimes','array'],
            'permissions.*' => ['integer','exists:permissions,id'],
        ]);

        DB::transaction(function () use ($request, $role, $data) {
            $antes = [
                'name'        => $role->name,
                'permissions' => $role->permissions()->pluck('id')->all(),
            ];

            if (array_key_exists('name', $data)) {
                $role->name = $data['name'];
                $role->save();
            }

            if (array_key_exists('permissions', $data)) {
                $perms = !empty($data['permissions'])
                    ? Permission::whereIn('id', $data['permissions'])->get()
                    : collect();
                $role->syncPermissions($perms);
            }

            $despues = [
                'name'        => $role->name,
                'permissions' => $role->permissions()->pluck('id')->all(),
            ];

            // Bitácora
            $this->logBitacora($request, [
                'accion'          => 'EDITAR_ROL',
                'modulo'          => 'ROLES_PERMISOS',
                'tabla_afectada'  => 'roles',
                'registro_id'     => $role->id,
                'descripcion'     => "Edición de rol {$role->name}",
                'cambios_antes'   => $antes,
                'cambios_despues' => $despues,
            ]);
        });

        return redirect()
            ->route('roles.show', $role)
            ->with('status', 'Rol actualizado.');
    }

    /**
     * Eliminar rol (opcional). Protege roles “núcleo” y roles en uso.
     */
    public function destroy(Role $role)
    {
        $this->ensureGuard($role);

        if (in_array($role->name, self::PROTECTED_ROLE_NAMES, true)) {
            return back()->withErrors(['role' => 'No se puede eliminar un rol núcleo del sistema.']);
        }

        // ¿El rol está asignado a alguien?
        $assigned = DB::table(config('permission.table_names.model_has_roles','model_has_roles'))
            ->where('role_id', $role->id)
            ->exists();

        if ($assigned) {
            return back()->withErrors(['role' => 'No se puede eliminar: el rol está asignado a usuarios.']);
        }

        $name = $role->name;
        $id   = $role->id;

        $role->delete();

        // Bitácora
        $this->logBitacora(request(), [
            'accion'         => 'ELIMINAR_ROL',
            'modulo'         => 'ROLES_PERMISOS',
            'tabla_afectada' => 'roles',
            'registro_id'    => $id,
            'descripcion'    => "Eliminación de rol {$name}",
        ]);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Rol eliminado.');
    }

    /** Asegura que el rol pertenece al guard actual (web). */
    private function ensureGuard(Role $role): void
    {
        $guard = config('auth.defaults.guard', 'web');
        abort_if($role->guard_name !== $guard, 404);
    }
}
