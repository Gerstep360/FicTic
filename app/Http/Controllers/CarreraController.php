<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Facultad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\LogsBitacora;

class CarreraController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // Solo autoridades pueden crear/editar (CU-02)
        $this->middleware(['permission:registrar_unidades_academicas'])->only(['create','store','edit','update']);
    }

    /**
     * Listar carreras (vista) con filtros: id_facultad, q, per_page.
     */
    public function index(Request $request)
    {
        $request->validate([
            'id_facultad' => ['sometimes','integer','exists:facultades,id_facultad'],
            'q'           => ['sometimes','string','max:100'],
            'per_page'    => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = Carrera::query()
            ->with(['facultad:id_facultad,nombre'])
            ->orderBy('nombre');

        if ($request->filled('id_facultad')) {
            $q->where('id_facultad', $request->integer('id_facultad'));
        }

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->whereRaw('LOWER(nombre) LIKE ?', [$like]);
        }

        $perPage  = (int) $request->get('per_page', 20);
        $carreras = $q->paginate($perPage)->appends($request->query());

        // Para filtros en la vista
        $facultades = Facultad::orderBy('nombre')->get(['id_facultad','nombre']);

        return view('carreras.index', compact('carreras','facultades'));
    }

    /** Form crear (incluye lista de facultades). */
    public function create()
    {
        $facultades = Facultad::orderBy('nombre')->get(['id_facultad','nombre']);
        return view('carreras.create', compact('facultades'));
    }

    /**
     * Crear carrera (CU-02).
     * Unicidad: nombre por facultad.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => [
                'required','string','max:100',
                Rule::unique('carreras','nombre')->where(fn($q) =>
                    $q->where('id_facultad', $request->input('id_facultad'))
                ),
            ],
            'id_facultad' => ['required','integer','exists:facultades,id_facultad'],
        ]);

        $carrera = Carrera::create($data);

        $this->logBitacora($request, [
            'accion'         => 'CREAR_CARRERA',
            'modulo'         => 'UNIDADES_ACADEMICAS',
            'tabla_afectada' => 'carreras',
            'registro_id'    => $carrera->id_carrera,
            'descripcion'    => "Creación de carrera {$carrera->nombre}",
            'metadata'       => ['payload' => $data],
        ]);

        return redirect()
            ->route('carreras.show', $carrera)
            ->with('status', 'Carrera creada correctamente.');
    }

    /** Detalle. */
    public function show(Carrera $carrera)
    {
        $carrera->load('facultad:id_facultad,nombre');
        return view('carreras.show', compact('carrera'));
    }

    /** Form editar. */
    public function edit(Carrera $carrera)
    {
        $facultades = Facultad::orderBy('nombre')->get(['id_facultad','nombre']);
        $carrera->load('facultad:id_facultad,nombre');
        return view('carreras.edit', compact('carrera','facultades'));
    }

    /**
     * Actualizar carrera (CU-02).
     * Mantiene la unicidad nombre+facultad.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $data = $request->validate([
            'nombre'      => [
                'sometimes','string','max:100',
                Rule::unique('carreras','nombre')
                    ->ignore($carrera->id_carrera, 'id_carrera')
                    ->where(fn($q) =>
                        $q->where('id_facultad', $request->input('id_facultad', $carrera->id_facultad))
                    ),
            ],
            'id_facultad' => ['sometimes','integer','exists:facultades,id_facultad'],
        ]);

        $antes = [
            'nombre'      => $carrera->nombre,
            'id_facultad' => $carrera->id_facultad,
        ];

        $carrera->fill($data)->save();

        $this->logBitacora($request, [
            'accion'          => 'EDITAR_CARRERA',
            'modulo'          => 'UNIDADES_ACADEMICAS',
            'tabla_afectada'  => 'carreras',
            'registro_id'     => $carrera->id_carrera,
            'descripcion'     => "Edición de carrera {$carrera->nombre}",
            'cambios_antes'   => $antes,
            'cambios_despues' => $carrera->only(['nombre','id_facultad']),
        ]);

        return redirect()
            ->route('carreras.show', $carrera)
            ->with('status', 'Carrera actualizada.');
    }
}
