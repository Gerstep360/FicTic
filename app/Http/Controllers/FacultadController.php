<?php

namespace App\Http\Controllers;

use App\Models\Facultad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\LogsBitacora;

class FacultadController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // Solo autoridades pueden crear/editar (CU-02)
        $this->middleware(['permission:registrar_unidades_academicas'])->only(['create','store','edit','update']);
    }

    /** Listado con filtros + paginación (vista). */
    public function index(Request $request)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:100'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = Facultad::query()->orderBy('nombre');

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->whereRaw('LOWER(nombre) LIKE ?', [$like]);
        }

        $perPage    = (int) $request->get('per_page', 20);
        $facultades = $q->paginate($perPage)->appends($request->query());

        return view('facultades.index', compact('facultades'));
    }

    /** Form crear. */
    public function create()
    {
        return view('facultades.create');
    }

    /** Crear facultad (CU-02). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required','string','max:100', Rule::unique('facultades','nombre')],
        ]);

        $facultad = Facultad::create($data);

        // Bitácora
        $this->logBitacora($request, [
            'accion'         => 'CREAR_FACULTAD',
            'modulo'         => 'UNIDADES_ACADEMICAS',
            'tabla_afectada' => 'facultades',
            'registro_id'    => $facultad->id_facultad,
            'descripcion'    => "Creación de facultad {$facultad->nombre}",
            'metadata'       => ['payload' => $data],
        ]);

        return redirect()
            ->route('facultades.show', $facultad)
            ->with('status', 'Facultad creada correctamente.');
    }

    /** Detalle. */
    public function show(Facultad $facultad)
    {
        return view('facultades.show', compact('facultad'));
    }

    /** Form editar. */
    public function edit(Facultad $facultad)
    {
        return view('facultades.edit', compact('facultad'));
    }

    /** Actualizar facultad (CU-02). */
    public function update(Request $request, Facultad $facultad)
    {
        $data = $request->validate([
            'nombre' => ['sometimes','string','max:100', Rule::unique('facultades','nombre')->ignore($facultad->id_facultad, 'id_facultad')],
        ]);

        $antes = $facultad->only(['nombre']);
        $facultad->fill($data)->save();

        // Bitácora
        $this->logBitacora($request, [
            'accion'          => 'EDITAR_FACULTAD',
            'modulo'          => 'UNIDADES_ACADEMICAS',
            'tabla_afectada'  => 'facultades',
            'registro_id'     => $facultad->id_facultad,
            'descripcion'     => "Edición de facultad {$facultad->nombre}",
            'cambios_antes'   => $antes,
            'cambios_despues' => $facultad->only(['nombre']),
        ]);

        return redirect()
            ->route('facultades.show', $facultad)
            ->with('status', 'Facultad actualizada.');
    }
}
