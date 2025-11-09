<?php

namespace App\Http\Controllers;

use App\Models\CargaDocente;
use App\Models\User;
use App\Models\Gestion;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Support\LogsBitacora;

class CargaDocenteController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // Solo coordinadores/directores/admin pueden gestionar carga docente (CU-13)
        $this->middleware(['permission:registrar_carga_docente'])->except(['index', 'show']);
    }

    /**
     * Listado de cargas docentes con filtros
     */
    public function index(Request $request)
    {
        $request->validate([
            'id_gestion' => ['sometimes', 'integer', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['sometimes', 'integer', 'exists:carreras,id_carrera'],
            'id_docente' => ['sometimes', 'integer', 'exists:users,id'],
            'q' => ['sometimes', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $q = CargaDocente::query()
            ->with(['docente:id,name,email', 'gestion:id_gestion,nombre', 'carrera:id_carrera,nombre'])
            ->orderBy('id_gestion', 'desc')
            ->orderBy('id_docente');

        if ($request->filled('id_gestion')) {
            $q->where('id_gestion', $request->integer('id_gestion'));
        }

        if ($request->filled('id_carrera')) {
            $q->where('id_carrera', $request->integer('id_carrera'));
        }

        if ($request->filled('id_docente')) {
            $q->where('id_docente', $request->integer('id_docente'));
        }

        if ($term = $request->get('q')) {
            $q->whereHas('docente', function ($qq) use ($term) {
                $like = '%' . mb_strtolower($term) . '%';
                $qq->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$like]);
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $cargas = $q->paginate($perPage)->appends($request->query());

        // Para filtros
        $gestiones = Gestion::orderByDesc('id_gestion')->get(['id_gestion', 'nombre']);
        $carreras = Carrera::orderBy('nombre')->get(['id_carrera', 'nombre']);

        return view('cargas-docentes.index', compact('cargas', 'gestiones', 'carreras'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $gestiones = Gestion::orderByDesc('id_gestion')->get(['id_gestion', 'nombre']);
        $carreras = Carrera::orderBy('nombre')->get(['id_carrera', 'nombre']);

        return view('cargas-docentes.create', compact('docentes', 'gestiones', 'carreras'));
    }

    /**
     * Guardar nueva carga docente (CU-13)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_docente' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('cargas_docentes', 'id_docente')
                    ->where('id_gestion', $request->input('id_gestion'))
                    ->where('id_carrera', $request->input('id_carrera')),
            ],
            'id_gestion' => ['required', 'integer', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['nullable', 'integer', 'exists:carreras,id_carrera'],
            'horas_contratadas' => ['required', 'integer', 'min:1', 'max:168'], // Máx 1 semana
            'tipo_contrato' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:50'],
            'restricciones_horario' => ['nullable', 'json'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'id_docente.unique' => 'Ya existe una carga para este docente en la gestión y carrera seleccionadas.',
        ]);

        $carga = CargaDocente::create($data);

        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'CREAR_CARGA_DOCENTE',
            'modulo' => 'CARGA_DOCENTE',
            'tabla_afectada' => 'cargas_docentes',
            'registro_id' => $carga->id_carga,
            'descripcion' => "Creación de carga docente para {$carga->docente->name} en gestión {$carga->gestion->nombre}",
            'id_gestion' => $carga->id_gestion,
            'metadata' => ['payload' => $data],
        ]);

        return redirect()
            ->route('cargas-docentes.show', $carga)
            ->with('status', 'Carga docente creada correctamente.');
    }

    /**
     * Ver detalle de carga docente
     */
    public function show(CargaDocente $cargaDocente)
    {
        $cargaDocente->load(['docente', 'gestion', 'carrera']);
        return view('cargas-docentes.show', compact('cargaDocente'));
    }

    /**
     * Formulario de edición
     */
    public function edit(CargaDocente $cargaDocente)
    {
        $cargaDocente->load(['docente', 'gestion', 'carrera']);

        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $gestiones = Gestion::orderByDesc('id_gestion')->get(['id_gestion', 'nombre']);
        $carreras = Carrera::orderBy('nombre')->get(['id_carrera', 'nombre']);

        return view('cargas-docentes.edit', compact('cargaDocente', 'docentes', 'gestiones', 'carreras'));
    }

    /**
     * Actualizar carga docente
     */
    public function update(Request $request, CargaDocente $cargaDocente)
    {
        $data = $request->validate([
            'id_docente' => [
                'sometimes',
                'integer',
                'exists:users,id',
                Rule::unique('cargas_docentes', 'id_docente')
                    ->where('id_gestion', $request->input('id_gestion', $cargaDocente->id_gestion))
                    ->where('id_carrera', $request->input('id_carrera', $cargaDocente->id_carrera))
                    ->ignore($cargaDocente->id_carga, 'id_carga'),
            ],
            'id_gestion' => ['sometimes', 'integer', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['nullable', 'integer', 'exists:carreras,id_carrera'],
            'horas_contratadas' => ['sometimes', 'integer', 'min:1', 'max:168'],
            'horas_asignadas' => ['sometimes', 'integer', 'min:0'],
            'tipo_contrato' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:50'],
            'restricciones_horario' => ['nullable', 'json'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $antes = $cargaDocente->only([
            'id_docente', 'id_gestion', 'id_carrera', 'horas_contratadas',
            'horas_asignadas', 'tipo_contrato', 'categoria', 'restricciones_horario', 'observaciones'
        ]);

        $cargaDocente->fill($data)->save();

        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'EDITAR_CARGA_DOCENTE',
            'modulo' => 'CARGA_DOCENTE',
            'tabla_afectada' => 'cargas_docentes',
            'registro_id' => $cargaDocente->id_carga,
            'descripcion' => "Edición de carga docente para {$cargaDocente->docente->name}",
            'id_gestion' => $cargaDocente->id_gestion,
            'cambios_antes' => $antes,
            'cambios_despues' => $cargaDocente->only(array_keys($antes)),
        ]);

        return redirect()
            ->route('cargas-docentes.show', $cargaDocente)
            ->with('status', 'Carga docente actualizada.');
    }

    /**
     * Eliminar carga docente
     */
    public function destroy(CargaDocente $cargaDocente)
    {
        // Verificar que no tenga horas asignadas
        if ($cargaDocente->horas_asignadas > 0) {
            return back()->withErrors([
                'carga' => 'No se puede eliminar una carga con horas ya asignadas en horarios.'
            ]);
        }

        $docente = $cargaDocente->docente->name;
        $id = $cargaDocente->id_carga;
        $idGestion = $cargaDocente->id_gestion;

        $cargaDocente->delete();

        // Bitácora
        $this->logBitacora(request(), [
            'accion' => 'ELIMINAR_CARGA_DOCENTE',
            'modulo' => 'CARGA_DOCENTE',
            'tabla_afectada' => 'cargas_docentes',
            'registro_id' => $id,
            'descripcion' => "Eliminación de carga docente para {$docente}",
            'id_gestion' => $idGestion,
        ]);

        return redirect()
            ->route('cargas-docentes.index')
            ->with('status', 'Carga docente eliminada.');
    }
}
