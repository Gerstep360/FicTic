<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Support\LogsBitacora;

class GrupoController extends Controller
{
    use LogsBitacora;

    // Catálogos cerrados
    private const TURNOS = ['Mañana','Tarde','Noche'];
    private const MODALIDADES = ['Presencial','Virtual','Laboratorio'];

    public function __construct()
    {
        $this->middleware(['auth']);

        // Puedes usar permisos granulares o el “paraguas” gestionar_grupos
        $this->middleware(['permission:ver_grupos|gestionar_grupos'])->only(['index']);
        $this->middleware(['permission:crear_grupos|gestionar_grupos'])->only(['create','store']);
        $this->middleware(['permission:editar_grupos|gestionar_grupos'])->only(['edit','update']);
        $this->middleware(['permission:eliminar_grupos|gestionar_grupos'])->only(['destroy']);
    }

    /**
     * Listado por materia y gestión (si no se pasa, toma la gestión “actual”).
     */
    public function index(Request $request, Carrera $carrera, Materia $materia)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);

        $gestionId = $request->integer('gestion') ?: $this->resolveActiveGestionId();
        abort_if(!$gestionId, 400, 'No hay gestión académica activa.');

        $q = Grupo::query()
            ->where('id_materia', $materia->id_materia)
            ->where('id_gestion', $gestionId);

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->whereRaw('LOWER(nombre_grupo) LIKE ?', [$like]);
        }

        $grupos = $q->orderBy('nombre_grupo')->paginate((int)$request->get('per_page', 20))
                    ->appends($request->query());

        // Renderiza tu vista (créala con tu estilo) o devuélvelo como JSON si prefieres:
        return view('grupos.index', [
            'carrera'   => $carrera,
            'materia'   => $materia,
            'grupos'    => $grupos,
            'gestionId' => $gestionId,
        ]);
    }

    public function create(Carrera $carrera, Materia $materia)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);

        $gestionId = $this->resolveActiveGestionId();
        abort_if(!$gestionId, 400, 'No hay gestión académica activa.');

        return view('grupos.create', [
            'carrera'    => $carrera,
            'materia'    => $materia,
            'gestionId'  => $gestionId,
            'turnos'     => self::TURNOS,
            'modalidades'=> self::MODALIDADES,
        ]);
    }

    public function store(Request $request, Carrera $carrera, Materia $materia)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);

        $gestionId = $request->integer('id_gestion') ?: $this->resolveActiveGestionId();
        abort_if(!$gestionId, 400, 'No hay gestión académica activa.');

        $data = $request->validate([
            'nombre_grupo' => [
                'required', 'string', 'max:10',
                Rule::unique('grupos', 'nombre_grupo')
                    ->where(fn($q)=> $q->where('id_materia', $materia->id_materia)->where('id_gestion', $gestionId)),
            ],
            'turno'       => ['required', 'string', Rule::in(self::TURNOS)],
            'modalidad'   => ['required', 'string', Rule::in(self::MODALIDADES)],
            'cupo'        => ['nullable', 'integer', 'min:1', 'max:1000'],
            'id_docente'  => ['nullable', 'integer', 'exists:users,id'],
        ]);

        // (Opcional) si se asigna docente, que tenga rol Docente
        if (!empty($data['id_docente'])) {
            $doc = User::find($data['id_docente']);
            if (!$doc || !$doc->hasRole('Docente')) {
                return back()->withErrors(['id_docente' => 'El docente seleccionado no tiene el rol Docente.'])->withInput();
            }
        }

        $grupo = null;
        DB::transaction(function () use (&$grupo, $materia, $gestionId, $data, $request) {
            $grupo = Grupo::create([
                'nombre_grupo' => $data['nombre_grupo'],
                'turno'        => $data['turno'],
                'modalidad'    => $data['modalidad'],
                'cupo'         => $data['cupo'] ?? null,
                'id_materia'   => $materia->id_materia,
                'id_gestion'   => $gestionId,
                'id_docente'   => $data['id_docente'] ?? null,
            ]);

            $this->logBitacora($request, [
                'accion'         => 'CREAR_GRUPO',
                'modulo'         => 'PROGRAMACION',
                'tabla_afectada' => 'grupos',
                'registro_id'    => $grupo->id_grupo,
                'descripcion'    => "Creación grupo {$grupo->nombre_grupo} en materia {$materia->id_materia} (gestión {$gestionId})",
                'metadata'       => ['payload' => $data],
            ]);
        });

        return redirect()
            ->route('carreras.materias.grupos.index', [$carrera, $materia, 'gestion' => $gestionId])
            ->with('status', 'Grupo creado');
    }

    public function edit(Carrera $carrera, Materia $materia, Grupo $grupo)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);
        abort_if((int)$grupo->id_materia !== (int)$materia->id_materia, 404);

        return view('grupos.edit', [
            'carrera'    => $carrera,
            'materia'    => $materia,
            'grupo'      => $grupo,
            'turnos'     => self::TURNOS,
            'modalidades'=> self::MODALIDADES,
        ]);
    }

    public function update(Request $request, Carrera $carrera, Materia $materia, Grupo $grupo)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);
        abort_if((int)$grupo->id_materia !== (int)$materia->id_materia, 404);

        $gestionId = (int) $grupo->id_gestion; // el grupo ya pertenece a una gestión

        $data = $request->validate([
            'nombre_grupo' => [
                'required', 'string', 'max:10',
                Rule::unique('grupos', 'nombre_grupo')
                    ->where(fn($q)=> $q->where('id_materia', $materia->id_materia)->where('id_gestion', $gestionId))
                    ->ignore($grupo->id_grupo, 'id_grupo'),
            ],
            'turno'       => ['required', 'string', Rule::in(self::TURNOS)],
            'modalidad'   => ['required', 'string', Rule::in(self::MODALIDADES)],
            'cupo'        => ['nullable', 'integer', 'min:1', 'max:1000'],
            'id_docente'  => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if (!empty($data['id_docente'])) {
            $doc = User::find($data['id_docente']);
            if (!$doc || !$doc->hasRole('Docente')) {
                return back()->withErrors(['id_docente' => 'El docente seleccionado no tiene el rol Docente.'])->withInput();
            }
        }

        DB::transaction(function () use ($grupo, $data, $request, $materia, $gestionId) {
            $grupo->update([
                'nombre_grupo' => $data['nombre_grupo'],
                'turno'        => $data['turno'],
                'modalidad'    => $data['modalidad'],
                'cupo'         => $data['cupo'] ?? null,
                'id_docente'   => $data['id_docente'] ?? null,
            ]);

            $this->logBitacora($request, [
                'accion'         => 'ACTUALIZAR_GRUPO',
                'modulo'         => 'PROGRAMACION',
                'tabla_afectada' => 'grupos',
                'registro_id'    => $grupo->id_grupo,
                'descripcion'    => "Actualización grupo {$grupo->nombre_grupo} en materia {$materia->id_materia} (gestión {$gestionId})",
                'metadata'       => ['payload' => $data],
            ]);
        });

        return redirect()
            ->route('carreras.materias.grupos.index', [$carrera, $materia, 'gestion' => $gestionId])
            ->with('status', 'Grupo actualizado');
    }

    public function destroy(Request $request, Carrera $carrera, Materia $materia, Grupo $grupo)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);
        abort_if((int)$grupo->id_materia !== (int)$materia->id_materia, 404);

        $grupo->delete();

        $this->logBitacora($request, [
            'accion'         => 'ELIMINAR_GRUPO',
            'modulo'         => 'PROGRAMACION',
            'tabla_afectada' => 'grupos',
            'registro_id'    => $grupo->id_grupo,
            'descripcion'    => "Baja lógica de grupo {$grupo->nombre_grupo} (materia {$materia->id_materia})",
        ]);

        return back()->with('status', 'Grupo eliminado');
    }

    /**
     * Resuelve la gestión “actual”.
     * 1) Intenta por fecha actual dentro del rango.
     * 2) Si no hay, toma la última creada (fallback).
     */
    private function resolveActiveGestionId(): ?int
    {
        $today = now()->toDateString();

        $id = DB::table('gestiones')
            ->whereDate('fecha_inicio', '<=', $today)
            ->whereDate('fecha_fin', '>=', $today)
            ->orderByDesc('fecha_inicio')
            ->value('id_gestion');

        if ($id) return (int)$id;

        return DB::table('gestiones')->orderByDesc('id_gestion')->value('id_gestion');
    }
    public function materias(Request $request, Carrera $carrera)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:100'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $gestionId = $this->resolveActiveGestionId();
        abort_if(!$gestionId, 400, 'No hay gestión académica activa.');

        $base = Materia::query()
            ->where('id_carrera', $carrera->id_carrera);

        if ($term = $request->q) {
            $like = '%'.mb_strtolower($term).'%';
            $base->where(function ($w) use ($like) {
                $w->whereRaw('LOWER(codigo) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', [$like]);
            });
        }

        // requiere relación grupos() en el modelo Materia (abajo)
        $base->withCount(['grupos as grupos_en_gestion' => function($q) use ($gestionId) {
            $q->where('id_gestion', $gestionId);
        }]);

        $materias = $base->orderBy('nombre')
            ->paginate((int)$request->get('per_page', 24))
            ->appends($request->query());

        return view('grupos.materias', compact('carrera','materias','gestionId'));
    }
}
