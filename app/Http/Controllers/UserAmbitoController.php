<?php

// app/Http/Controllers/UserAmbitoController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAmbito;
use App\Models\Carrera;
use App\Models\Facultad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Support\LogsBitacora;
class UserAmbitoController extends Controller
{
    use LogsBitacora;

    private array $adminNames;
    private array $order;
    private array $allowedMap;
    private array $defaults;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:ver_asignaciones_ambito'])->only(['browse','index']);
        $this->middleware(['permission:asignar_perfiles_ambitos'])->only(['store']);
        $this->middleware(['permission:eliminar_asignacion_ambito'])->only(['destroy']);

        // Carga config una vez
        $this->adminNames = config('ambitos.admin_role_names', ['Admin DTIC']);
        $this->order      = config('ambitos.order', []);
        $this->allowedMap = config('ambitos.allowed_scopes', []);
        $this->defaults   = config('ambitos.defaults', []);
    }

    // -------------------- LISTA DE USUARIOS --------------------
    public function browse(Request $request)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:100'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = User::query()->with(['roles:id,name']);

        if ($term = $request->q) {
            $like = '%'.mb_strtolower($term).'%';
            $q->where(fn($w) =>
                $w->whereRaw('LOWER(name) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$like])
            );
        }

        $users = $q->orderBy('name')->paginate((int)$request->get('per_page', 20))
                   ->appends($request->query());

        return view('usuarios.ambitos.browse', compact('users'));
    }

    // -------------------- PANTALLA POR USUARIO --------------------
    public function index(User $user)
    {
        $ambitos = UserAmbito::with('role:id,name','scope')
            ->where('user_id', $user->id)
            ->get();

        $userRoleNames = $user->getRoleNames()->all();
        $maxRank       = $this->maxRankFor($userRoleNames);

        $rolesQuery = Role::orderBy('name');

        // Mostrar solo roles que YA tiene el usuario (evita crear roles aquí)
        if ($this->defaults['limit_to_existing_roles'] ?? true) {
            $rolesQuery->whereIn('name', $userRoleNames ?: ['__none__']);
        }

        // Operador no-admin no ve 'Admin DTIC'
        if (!auth()->user()->hasAnyRole($this->adminNames)) {
            $rolesQuery->whereNotIn('name', $this->adminNames);
        }

        // Solo roles del mayor rango que ya posee
        if (($this->defaults['enforce_top_role'] ?? true) && !empty($userRoleNames)) {
            $topNames = array_values(array_filter($userRoleNames, fn($n) => $this->rank($n) === $maxRank));
            $rolesQuery->whereIn('name', $topNames ?: ['__none__']);
        }

        $roles      = $rolesQuery->get(['id','name','guard_name']);
        $carreras   = Carrera::orderBy('nombre')->get(['id_carrera','nombre']);
        $facultades = Facultad::orderBy('nombre')->get(['id_facultad','nombre']);

        return view('usuarios.ambitos.index', compact('user','ambitos','roles','carreras','facultades'));
    }

    // -------------------- ASIGNAR ÁMBITO --------------------
    public function store(Request $request, User $user)
    {
        $guard = config('auth.defaults.guard', 'web');

        $data = $request->validate([
            'role_id'    => ['required','integer', Rule::exists('roles','id')->where('guard_name',$guard)],
            'scope_type' => ['nullable','string'], // null para roles sin ámbito
            'scope_id'   => ['nullable','integer'],
        ]);

        $role = Role::findOrFail($data['role_id']);

        // ¿el operador puede otorgar este rol?
        $allowedRoleIds = $this->rolesPermitidosParaOperadorYUsuario($user)->pluck('id')->all();
        if (!in_array($role->id, $allowedRoleIds, true)) {
            return back()->withErrors(['role_id' => 'No puedes asignar este rol.']);
        }

        // Top-role enforcement
        if (($this->defaults['enforce_top_role'] ?? true)) {
            $userRoleNames = $user->getRoleNames()->all();
            $maxRank       = $this->maxRankFor($userRoleNames);
            if ($this->rank($role->name) < $maxRank) {
                return back()->withErrors([
                    'role_id' => 'El usuario tiene un rol de mayor jerarquía; solo se permite asignar ámbito a ese rol superior.',
                ]);
            }
        }

        // Roles admin: sin ámbito
        if (in_array($role->name, $this->adminNames, true)) {
            if (!$user->hasRole($role->name)) $user->assignRole($role->name);
            $this->logBitacora($request, [
                'accion' => 'ASIGNAR_ROL_ADMIN','modulo'=>'ROLES_PERMISOS','tabla_afectada'=>'roles',
                'descripcion' => "Asignación de rol {$role->name} al usuario {$user->id} (sin ámbito).",
            ]);
            return back()->with('status', "{$role->name} asignado (sin ámbito).");
        }

        // Normaliza scope_type -> FQCN
        $aliases = [
            'user'     => User::class,     User::class     => User::class,
            'carrera'  => Carrera::class,  Carrera::class  => Carrera::class,
            'facultad' => Facultad::class, Facultad::class => Facultad::class,
        ];
        if (empty($data['scope_type']) || !isset($aliases[$data['scope_type']])) {
            return back()->withErrors(['scope_type' => 'Debes seleccionar un ámbito válido.'])->withInput();
        }
        $data['scope_type'] = $aliases[$data['scope_type']];

        // Chequea que el tipo de ámbito esté permitido para ese rol (desde config)
        $allowedAliases = $this->allowedScopeAliasesFor($role->name);
        $allowedFQCN    = array_map(fn($a) => $aliases[$a], $allowedAliases);
        if (!in_array($data['scope_type'], $allowedFQCN, true)) {
            return back()->withErrors(['scope_type' => "El rol {$role->name} no acepta ese tipo de ámbito."]);
        }

        // Validación de existencia
        $exists = match ($data['scope_type']) {
            User::class     => User::whereKey($data['scope_id'])->exists(),
            Carrera::class  => Carrera::whereKey($data['scope_id'])->exists(),
            Facultad::class => Facultad::whereKey($data['scope_id'])->exists(),
        };
        if (!$exists) {
            return back()->withErrors(['scope_id' => 'El ámbito seleccionado no existe.'])->withInput();
        }

        // Regla especial Docente → propio
        if ($role->name === 'Docente' && (int)$data['scope_id'] !== (int)$user->id) {
            return back()->withErrors(['scope_id' => 'Para Docente, el ámbito debe ser el propio usuario.'])->withInput();
        }

        // Crear/asegurar
        $ambito = UserAmbito::firstOrCreate([
            'user_id'    => $user->id,
            'role_id'    => $role->id,
            'scope_type' => $data['scope_type'],
            'scope_id'   => $data['scope_id'],
        ]);

        if (!$user->hasRole($role->name)) $user->assignRole($role->name);

        // (Opcional) limpiar ámbitos de roles inferiores si asignaste a uno superior
        $this->purgeLowerRankScopes($user, $this->rank($role->name));

        $this->logBitacora($request, [
            'accion' => 'ASIGNAR_AMBITO','modulo'=>'ROLES_PERMISOS','tabla_afectada'=>'user_ambitos',
            'registro_id' => $ambito->id ?? null,
            'descripcion' => "Ámbito {$role->name} → {$data['scope_type']}#{$data['scope_id']} al usuario {$user->id}",
        ]);

        return back()->with('status','Ámbito asignado');
    }

    public function destroy(User $user, UserAmbito $ambito)
    {
        abort_if($ambito->user_id !== $user->id, 404);
        $ambito->delete();

        $this->logBitacora(request(), [
            'accion'=>'ELIMINAR_AMBITO','modulo'=>'ROLES_PERMISOS','tabla_afectada'=>'user_ambitos',
            'registro_id'=>$ambito->id ?? null,'descripcion'=>"Eliminación de ámbito del usuario {$user->id}",
        ]);

        return back()->with('status','Ámbito eliminado');
    }

    // -------------------- Helpers --------------------
    private function rolesPermitidosParaOperadorYUsuario(User $user)
    {
        $q = Role::query();

        if (!auth()->user()->hasAnyRole($this->adminNames)) {
            $q->whereNotIn('name', $this->adminNames);
        }

        if ($this->defaults['limit_to_existing_roles'] ?? true) {
            $q->whereIn('name', $user->getRoleNames());
        }

        return $q->get(['id','name','guard_name']);
    }

    private function rank(string $roleName): int
    {
        // índice en 'order' (0 = bajo, mayor = alto)
        $idx = array_search($roleName, $this->order, true);
        return $idx === false ? -1 : $idx;
    }

    private function maxRankFor(array $roleNames): int
    {
        $ranks = array_map(fn($n) => $this->rank($n), $roleNames);
        return $ranks ? max($ranks) : -1;
    }

    private function allowedScopeAliasesFor(string $roleName): array
    {
        $aliases = $this->allowedMap[$roleName] ?? ($this->defaults['allowed_scopes'] ?? ['facultad','carrera','user']);
        // normaliza y quita duplicados
        return array_values(array_unique(array_map('strtolower', $aliases)));
    }

    private function purgeLowerRankScopes(User $user, int $keepRank): void
    {
        $lowerRoleIds = Role::whereIn('name', $user->getRoleNames())
            ->get()
            ->filter(fn($r) => $this->rank($r->name) < $keepRank)
            ->pluck('id');

        if ($lowerRoleIds->isNotEmpty()) {
            UserAmbito::where('user_id', $user->id)
                ->whereIn('role_id', $lowerRoleIds->all())
                ->delete();
        }
    }
}