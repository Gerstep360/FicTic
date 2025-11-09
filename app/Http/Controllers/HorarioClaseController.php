<?php

namespace App\Http\Controllers;

use App\Models\HorarioClase;
use App\Models\Grupo;
use App\Models\Aula;
use App\Models\Bloque;
use App\Models\User;
use App\Models\Gestion;
use App\Models\Carrera;
use App\Models\CargaDocente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\LogsBitacora;

class HorarioClaseController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // CU-14: Solo coordinadores/directores/admin pueden asignar horarios
        $this->middleware(['permission:asignar_horarios'])->except(['index', 'show']);
    }

    /**
     * Vista principal: cuadrícula de horarios por carrera/gestión
     */
    public function index(Request $request)
    {
        $request->validate([
            'id_gestion' => ['sometimes', 'integer', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['sometimes', 'integer', 'exists:carreras,id_carrera'],
            'id_aula' => ['sometimes', 'integer', 'exists:aulas,id_aula'],
            'id_docente' => ['sometimes', 'integer', 'exists:users,id'],
        ]);

        // Obtener filtros
        $gestiones = Gestion::orderByDesc('id_gestion')->get(['id_gestion', 'nombre']);
        $carreras = Carrera::orderBy('nombre')->get(['id_carrera', 'nombre']);
        $aulas = Aula::orderBy('codigo')->get(['id_aula', 'codigo', 'tipo']);
        $bloques = Bloque::orderBy('hora_inicio')->get();
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
            ->orderBy('name')
            ->get(['id', 'name']);

        // Construir query de horarios
        $q = HorarioClase::query()
            ->with([
                'grupo.materia',
                'docente:id,name',
                'aula:id_aula,codigo,tipo',
                'bloque:id_bloque,hora_inicio,hora_fin,etiqueta'
            ]);

        // Filtrar por gestión (a través del grupo)
        if ($request->filled('id_gestion')) {
            $q->whereHas('grupo', function ($qq) use ($request) {
                $qq->where('id_gestion', $request->integer('id_gestion'));
            });
        }

        // Filtrar por carrera (a través del grupo → materia)
        if ($request->filled('id_carrera')) {
            $q->whereHas('grupo.materia', function ($qq) use ($request) {
                $qq->where('id_carrera', $request->integer('id_carrera'));
            });
        }

        if ($request->filled('id_aula')) {
            $q->where('id_aula', $request->integer('id_aula'));
        }

        if ($request->filled('id_docente')) {
            $q->where('id_docente', $request->integer('id_docente'));
        }

        $horarios = $q->orderBy('dia_semana')
            ->orderBy('id_bloque')
            ->get();

        // Organizar horarios en matriz [dia][bloque]
        $matriz = $this->construirMatrizHorarios($horarios, $bloques);

        return view('horarios.index', compact(
            'matriz',
            'bloques',
            'gestiones',
            'carreras',
            'aulas',
            'docentes'
        ));
    }

    /**
     * Formulario de asignación manual
     */
    public function create(Request $request)
    {
        // Obtener grupos sin horario asignado o con horarios incompletos
        $grupos = Grupo::with(['materia.carrera', 'docente'])
            ->when($request->filled('id_gestion'), function ($q) use ($request) {
                $q->where('id_gestion', $request->integer('id_gestion'));
            })
            ->orderBy('id_materia')
            ->orderBy('nombre_grupo')
            ->get();

        $gestiones = Gestion::orderByDesc('id_gestion')->get(['id_gestion', 'nombre']);
        $carreras = Carrera::orderBy('nombre')->get(['id_carrera', 'nombre']);
        $aulas = Aula::orderBy('codigo')->get();
        $bloques = Bloque::orderBy('hora_inicio')->get();
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('horarios.create', compact(
            'grupos',
            'gestiones',
            'carreras',
            'aulas',
            'bloques',
            'docentes'
        ));
    }

    /**
     * Guardar asignación de horario con validación de conflictos (CU-14)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_grupo' => ['required', 'integer', 'exists:grupos,id_grupo'],
            'id_docente' => ['required', 'integer', 'exists:users,id'],
            'id_aula' => ['required', 'integer', 'exists:aulas,id_aula'],
            'id_bloque' => ['required', 'integer', 'exists:bloques,id_bloque'],
            'dia_semana' => ['required', 'integer', 'min:1', 'max:7'],
        ]);

        // Validar conflictos
        $conflictos = $this->validarConflictos(
            $data['dia_semana'],
            $data['id_bloque'],
            $data['id_aula'],
            $data['id_docente'],
            null // no hay id_horario porque es nuevo
        );

        if (!empty($conflictos)) {
            return back()
                ->withErrors(['conflictos' => $conflictos])
                ->withInput();
        }

        // Validar carga docente (no exceder horas contratadas)
        $grupo = Grupo::with('materia')->findOrFail($data['id_grupo']);
        $alertaCarga = $this->validarCargaDocente($data['id_docente'], $grupo->id_gestion);

        $horario = HorarioClase::create($data);

        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'ASIGNAR_HORARIO',
            'modulo' => 'HORARIOS',
            'tabla_afectada' => 'horario_clases',
            'registro_id' => $horario->id_horario,
            'descripcion' => "Asignación de horario para grupo {$grupo->nombre_grupo} - {$grupo->materia->nombre}",
            'id_gestion' => $grupo->id_gestion,
            'metadata' => [
                'payload' => $data,
                'alerta_carga' => $alertaCarga,
            ],
        ]);

        $mensaje = 'Horario asignado correctamente.';
        if ($alertaCarga) {
            $mensaje .= ' ' . $alertaCarga;
        }

        return redirect()
            ->route('horarios.index', ['id_gestion' => $grupo->id_gestion])
            ->with('status', $mensaje);
    }

    /**
     * Ver detalle de un horario
     */
    public function show(HorarioClase $horario)
    {
        $horario->load([
            'grupo.materia.carrera',
            'grupo.gestion',
            'docente',
            'aula',
            'bloque'
        ]);

        return view('horarios.show', compact('horario'));
    }

    /**
     * Formulario de edición
     */
    public function edit(HorarioClase $horario)
    {
        $horario->load([
            'grupo.materia.carrera',
            'grupo.gestion',
            'docente',
            'aula',
            'bloque'
        ]);

        $aulas = Aula::orderBy('codigo')->get();
        $bloques = Bloque::orderBy('hora_inicio')->get();
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('horarios.edit', compact('horario', 'aulas', 'bloques', 'docentes'));
    }

    /**
     * Actualizar horario con validación de conflictos
     */
    public function update(Request $request, HorarioClase $horario)
    {
        $data = $request->validate([
            'id_docente' => ['sometimes', 'integer', 'exists:users,id'],
            'id_aula' => ['sometimes', 'integer', 'exists:aulas,id_aula'],
            'id_bloque' => ['sometimes', 'integer', 'exists:bloques,id_bloque'],
            'dia_semana' => ['sometimes', 'integer', 'min:1', 'max:7'],
        ]);

        // Si se cambió día, bloque, aula o docente, validar conflictos
        $cambioHorario = isset($data['dia_semana']) || isset($data['id_bloque']) ||
                         isset($data['id_aula']) || isset($data['id_docente']);

        if ($cambioHorario) {
            $conflictos = $this->validarConflictos(
                $data['dia_semana'] ?? $horario->dia_semana,
                $data['id_bloque'] ?? $horario->id_bloque,
                $data['id_aula'] ?? $horario->id_aula,
                $data['id_docente'] ?? $horario->id_docente,
                $horario->id_horario // excluir el horario actual
            );

            if (!empty($conflictos)) {
                return back()
                    ->withErrors(['conflictos' => $conflictos])
                    ->withInput();
            }
        }

        $antes = $horario->only(['id_docente', 'id_aula', 'id_bloque', 'dia_semana']);
        $horario->fill($data)->save();

        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'EDITAR_HORARIO',
            'modulo' => 'HORARIOS',
            'tabla_afectada' => 'horario_clases',
            'registro_id' => $horario->id_horario,
            'descripcion' => "Modificación de horario ID {$horario->id_horario}",
            'id_gestion' => $horario->grupo->id_gestion,
            'cambios_antes' => $antes,
            'cambios_despues' => $horario->only(array_keys($antes)),
        ]);

        return redirect()
            ->route('horarios.show', $horario)
            ->with('status', 'Horario actualizado correctamente.');
    }

    /**
     * Eliminar asignación de horario
     */
    public function destroy(HorarioClase $horario)
    {
        $grupo = $horario->grupo;
        $id = $horario->id_horario;
        $idGestion = $grupo->id_gestion;

        $horario->delete();

        // Bitácora
        $this->logBitacora(request(), [
            'accion' => 'ELIMINAR_HORARIO',
            'modulo' => 'HORARIOS',
            'tabla_afectada' => 'horario_clases',
            'registro_id' => $id,
            'descripcion' => "Eliminación de asignación de horario para grupo {$grupo->nombre_grupo}",
            'id_gestion' => $idGestion,
        ]);

        return redirect()
            ->route('horarios.index', ['id_gestion' => $idGestion])
            ->with('status', 'Horario eliminado correctamente.');
    }

    /**
     * API: Validar conflictos en tiempo real (AJAX)
     */
    public function validarConflictosAjax(Request $request)
    {
        $request->validate([
            'dia_semana' => ['required', 'integer', 'min:1', 'max:7'],
            'id_bloque' => ['required', 'integer', 'exists:bloques,id_bloque'],
            'id_aula' => ['required', 'integer', 'exists:aulas,id_aula'],
            'id_docente' => ['required', 'integer', 'exists:users,id'],
            'id_horario' => ['nullable', 'integer', 'exists:horario_clases,id_horario'],
        ]);

        $conflictos = $this->validarConflictos(
            $request->integer('dia_semana'),
            $request->integer('id_bloque'),
            $request->integer('id_aula'),
            $request->integer('id_docente'),
            $request->integer('id_horario')
        );

        return response()->json([
            'valido' => empty($conflictos),
            'conflictos' => $conflictos,
        ]);
    }

    /**
     * Validar conflictos de horario (aula y docente)
     */
    protected function validarConflictos(
        int $diaSemana,
        int $idBloque,
        int $idAula,
        int $idDocente,
        ?int $idHorarioActual = null
    ): array {
        $conflictos = [];

        // Conflicto de aula
        $conflictoAula = HorarioClase::where('dia_semana', $diaSemana)
            ->where('id_bloque', $idBloque)
            ->where('id_aula', $idAula)
            ->when($idHorarioActual, fn($q) => $q->where('id_horario', '!=', $idHorarioActual))
            ->with(['grupo.materia', 'docente'])
            ->first();

        if ($conflictoAula) {
            $conflictos[] = sprintf(
                'El aula ya está ocupada por el grupo %s (%s) con el docente %s.',
                $conflictoAula->grupo->nombre_grupo,
                $conflictoAula->grupo->materia->nombre,
                $conflictoAula->docente->name
            );
        }

        // Conflicto de docente
        $conflictoDocente = HorarioClase::where('dia_semana', $diaSemana)
            ->where('id_bloque', $idBloque)
            ->where('id_docente', $idDocente)
            ->when($idHorarioActual, fn($q) => $q->where('id_horario', '!=', $idHorarioActual))
            ->with(['grupo.materia', 'aula'])
            ->first();

        if ($conflictoDocente) {
            $conflictos[] = sprintf(
                'El docente ya tiene clase con el grupo %s (%s) en el aula %s.',
                $conflictoDocente->grupo->nombre_grupo,
                $conflictoDocente->grupo->materia->nombre,
                $conflictoDocente->aula->codigo
            );
        }

        return $conflictos;
    }

    /**
     * Validar que el docente no exceda su carga contratada
     */
    protected function validarCargaDocente(int $idDocente, int $idGestion): ?string
    {
        $carga = CargaDocente::where('id_docente', $idDocente)
            ->where('id_gestion', $idGestion)
            ->first();

        if (!$carga) {
            return 'Advertencia: El docente no tiene carga registrada para esta gestión.';
        }

        // Contar horas ya asignadas
        $horasAsignadas = HorarioClase::whereHas('grupo', function ($q) use ($idGestion) {
            $q->where('id_gestion', $idGestion);
        })
            ->where('id_docente', $idDocente)
            ->count(); // Cada horario = 1 hora (ajustar según tu lógica)

        if ($horasAsignadas >= $carga->horas_contratadas) {
            return sprintf(
                'Advertencia: El docente ya tiene %d horas asignadas de %d contratadas. Se está excediendo la carga.',
                $horasAsignadas,
                $carga->horas_contratadas
            );
        }

        if ($horasAsignadas >= $carga->horas_contratadas * 0.9) {
            return sprintf(
                'Advertencia: El docente está cerca del límite (%d/%d horas).',
                $horasAsignadas,
                $carga->horas_contratadas
            );
        }

        return null;
    }

    /**
     * Construir matriz de horarios [dia][bloque] para la vista
     */
    protected function construirMatrizHorarios($horarios, $bloques): array
    {
        $matriz = [];
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        foreach ($horarios as $horario) {
            $dia = $horario->dia_semana;
            $bloque = $horario->id_bloque;

            if (!isset($matriz[$dia])) {
                $matriz[$dia] = ['nombre' => $dias[$dia] ?? "Día $dia", 'bloques' => []];
            }

            $matriz[$dia]['bloques'][$bloque] = $horario;
        }

        return $matriz;
    }
}
