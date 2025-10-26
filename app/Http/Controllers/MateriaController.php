<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Support\LogsBitacora;

class MateriaController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // Puedes usar permisos granulares o uno paraguas "gestionar_asignaturas"
        $this->middleware(['permission:ver_materias|gestionar_asignaturas'])->only(['index']);
        $this->middleware(['permission:crear_materias|gestionar_asignaturas'])->only(['create','store']);
        $this->middleware(['permission:editar_materias|gestionar_asignaturas'])->only(['edit','update']);
        $this->middleware(['permission:eliminar_materias|gestionar_asignaturas'])->only(['destroy']);
        $this->middleware(['permission:restaurar_materias|gestionar_asignaturas'])->only(['restore']);
    }

    // Listado por carrera
    public function index(Request $request, Carrera $carrera)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:100'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
            'with_trash' => ['sometimes','boolean'],
        ]);

        $base = Materia::query()->where('id_carrera', $carrera->id_carrera);

        if ($term = $request->q) {
            $like = '%'.mb_strtolower($term).'%';
            $base->where(function ($w) use ($like) {
                $w->whereRaw('LOWER(codigo) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', [$like]);
            });
        }

        if ($request->boolean('with_trash')) {
            $base->withTrashed();
        }

        $materias = $base->orderBy('nombre')->paginate((int)$request->get('per_page', 20))
                         ->appends($request->query());

        return view('materias.index', compact('carrera','materias')); // crea tu blade con tu estilo
    }

    public function create(Carrera $carrera)
    {
        $materiasDeCarrera = Materia::where('id_carrera', $carrera->id_carrera)->orderBy('nombre')->get(['id_materia','nombre','codigo']);
        return view('materias.create', compact('carrera','materiasDeCarrera'));
    }

    public function store(Request $request, Carrera $carrera)
    {
        $data = $request->validate([
            'codigo'   => [
                'required','string','max:20',
                Rule::unique('materias','codigo')->where(fn($q)=>$q->where('id_carrera',$carrera->id_carrera)),
            ],
            'nombre'   => ['required','string','max:100'],
            'nivel'    => ['nullable','string','max:20'],
            'creditos' => ['required','integer','min:0','max:99'],
            'prerrequisitos' => ['sometimes','array'],         // ids
            'prerrequisitos.*' => ['integer'],
        ]);

        // Validar que los prerrequisitos (si vienen) son de la misma carrera
        $prReqIds = collect($data['prerrequisitos'] ?? [])->unique()->values()->all();
        if ($prReqIds) {
            $validCount = Materia::where('id_carrera', $carrera->id_carrera)
                ->whereIn('id_materia', $prReqIds)->count();
            if ($validCount !== count($prReqIds)) {
                return back()->withErrors(['prerrequisitos' => 'Todos los prerrequisitos deben pertenecer a la misma carrera.'])->withInput();
            }
        }

        $materia = null;

        DB::transaction(function () use (&$materia, $carrera, $data, $prReqIds, $request) {
            $materia = Materia::create([
                'codigo' => $data['codigo'],
                'nombre' => $data['nombre'],
                'nivel'  => $data['nivel'] ?? 'Licenciatura',
                'creditos' => $data['creditos'],
                'id_carrera' => $carrera->id_carrera,
            ]);

            if (!empty($prReqIds)) {
                $materia->prerrequisitos()->sync($prReqIds);
            }

            $this->logBitacora($request, [
                'accion' => 'CREAR_MATERIA',
                'modulo' => 'PLAN_ESTUDIOS',
                'tabla_afectada' => 'materias',
                'registro_id' => $materia->id_materia,
                'descripcion' => "Creación de materia {$materia->codigo} - {$materia->nombre} en carrera {$carrera->id_carrera}",
                'metadata' => ['payload' => $data],
            ]);
        });

        return redirect()
            ->route('carreras.materias.index', $carrera)
            ->with('status','Materia creada');
    }

    public function edit(Carrera $carrera, Materia $materia)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);

        $materiasDeCarrera = Materia::where('id_carrera', $carrera->id_carrera)
            ->where('id_materia', '!=', $materia->id_materia)
            ->orderBy('nombre')->get(['id_materia','nombre','codigo']);

        $prerrequisitosSeleccionados = $materia->prerrequisitos()->pluck('materias.id_materia')->all();

        return view('materias.edit', compact('carrera','materia','materiasDeCarrera','prerrequisitosSeleccionados'));
    }

    public function update(Request $request, Carrera $carrera, Materia $materia)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);

        $data = $request->validate([
            'codigo'   => [
                'required','string','max:20',
                Rule::unique('materias','codigo')
                    ->where(fn($q)=>$q->where('id_carrera',$carrera->id_carrera))
                    ->ignore($materia->id_materia, 'id_materia'),
            ],
            'nombre'   => ['required','string','max:100'],
            'nivel'    => ['nullable','string','max:20'],
            'creditos' => ['required','integer','min:0','max:99'],
            'prerrequisitos' => ['sometimes','array'],
            'prerrequisitos.*' => ['integer','different:'.$materia->id_materia],
        ]);

        $prReqIds = collect($data['prerrequisitos'] ?? [])->unique()->values()->all();
        if ($prReqIds) {
            $validCount = Materia::where('id_carrera', $carrera->id_carrera)
                ->whereIn('id_materia', $prReqIds)->count();
            if ($validCount !== count($prReqIds)) {
                return back()->withErrors(['prerrequisitos' => 'Todos los prerrequisitos deben pertenecer a la misma carrera.'])->withInput();
            }
        }

        DB::transaction(function () use ($materia, $data, $prReqIds, $request, $carrera) {
            $materia->update([
                'codigo'   => $data['codigo'],
                'nombre'   => $data['nombre'],
                'nivel'    => $data['nivel'] ?? 'Licenciatura',
                'creditos' => $data['creditos'],
            ]);

            $materia->prerrequisitos()->sync($prReqIds);

            $this->logBitacora($request, [
                'accion' => 'ACTUALIZAR_MATERIA',
                'modulo' => 'PLAN_ESTUDIOS',
                'tabla_afectada' => 'materias',
                'registro_id' => $materia->id_materia,
                'descripcion' => "Actualización de materia {$materia->codigo} en carrera {$carrera->id_carrera}",
                'metadata' => ['payload' => $data],
            ]);
        });

        return redirect()
            ->route('carreras.materias.index', $carrera)
            ->with('status','Materia actualizada');
    }

    // Baja lógica
    public function destroy(Request $request, Carrera $carrera, Materia $materia)
    {
        abort_if((int)$materia->id_carrera !== (int)$carrera->id_carrera, 404);

        $materia->delete();

        $this->logBitacora($request, [
            'accion' => 'ELIMINAR_MATERIA',
            'modulo' => 'PLAN_ESTUDIOS',
            'tabla_afectada' => 'materias',
            'registro_id' => $materia->id_materia,
            'descripcion' => "Baja lógica de materia {$materia->codigo} en carrera {$carrera->id_carrera}",
        ]);

        return back()->with('status','Materia eliminada');
    }

    // (Opcional) Restaurar baja lógica
    public function restore(Request $request, Carrera $carrera, $id_materia)
    {
        $materia = Materia::onlyTrashed()
            ->where('id_materia', $id_materia)
            ->where('id_carrera', $carrera->id_carrera)
            ->firstOrFail();

        $materia->restore();

        $this->logBitacora($request, [
            'accion' => 'RESTAURAR_MATERIA',
            'modulo' => 'PLAN_ESTUDIOS',
            'tabla_afectada' => 'materias',
            'registro_id' => $materia->id_materia,
            'descripcion' => "Restauración de materia {$materia->codigo} en carrera {$carrera->id_carrera}",
        ]);

        return back()->with('status','Materia restaurada');
    }
}
