<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use App\Models\Feriado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Support\LogsBitacora;

class GestionController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        // Solo autoridades (Decano/DTIC) pueden abrir/editar gestiones (CU-01)
        $this->middleware(['permission:abrir_gestion'])->only(['create','store','edit','update']);
    }

    /**
     * Vista: lista paginada de gestiones (consulta administrativa).
     */
    public function index(Request $request)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:50'],
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = Gestion::query()->orderByDesc('id_gestion');

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->whereRaw('LOWER(nombre) LIKE ?', [$like]);
        }

        $perPage   = (int) $request->get('per_page', 15);
        $gestiones = $q->paginate($perPage)->appends($request->query());

        return view('gestiones.index', compact('gestiones'));
    }

    /**
     * Vista: formulario para abrir una nueva gestión (CU-01).
     */
    public function create()
    {
        return view('gestiones.create');
    }

    /**
     * CU-01 Abrir Gestión Académica: crea gestión + registra feriados.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                   => ['required','string','max:50', Rule::unique('gestiones','nombre')],
            'fecha_inicio'             => ['required','date'],
            'fecha_fin'                => ['required','date','after:fecha_inicio'],
            'feriados'                 => ['sometimes','array'],
            'feriados.*.fecha'         => ['required_with:feriados','date'],
            'feriados.*.descripcion'   => ['nullable','string','max:120'],
        ]);

        // Cada feriado debe caer dentro [fecha_inicio, fecha_fin]
        if (!empty($data['feriados'])) {
            foreach ($data['feriados'] as $f) {
                if (isset($f['fecha']) && ($f['fecha'] < $data['fecha_inicio'] || $f['fecha'] > $data['fecha_fin'])) {
                    return back()
                        ->withErrors(['feriados' => "El feriado {$f['fecha']} está fuera del rango de la gestión."])
                        ->withInput();
                }
            }
        }

        $gestion = DB::transaction(function () use ($data, $request) {
            // Crear gestión (publicada=false)
            $g = Gestion::create([
                'nombre'       => $data['nombre'],
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin'    => $data['fecha_fin'],
                'publicada'    => false,
            ]);

            // Insertar feriados (si vienen)
            if (!empty($data['feriados'])) {
                $rows = [];
                foreach ($data['feriados'] as $f) {
                    $rows[] = [
                        'id_gestion'  => $g->id_gestion,
                        'fecha'       => $f['fecha'],
                        'descripcion' => $f['descripcion'] ?? null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
                Feriado::insert($rows);
            }

            // Bitácora (con IP/UA/dispositivo vía BitacoraController@store)
            $this->logBitacora($request, [
                'accion'         => 'ABRIR_GESTION',
                'modulo'         => 'GESTIONES',
                'tabla_afectada' => 'gestiones',
                'registro_id'    => $g->id_gestion,
                'descripcion'    => "Apertura de gestión {$g->nombre} ({$g->fecha_inicio} → {$g->fecha_fin})",
                'id_gestion'     => $g->id_gestion,
                'metadata'       => [
                    'payload'  => [
                        'nombre'       => $g->nombre,
                        'fecha_inicio' => $g->fecha_inicio,
                        'fecha_fin'    => $g->fecha_fin,
                    ],
                    'feriados' => $data['feriados'] ?? [],
                ],
            ]);

            return $g;
        });

        return redirect()
            ->route('gestiones.show', $gestion)
            ->with('status', 'Gestión creada correctamente.');
    }

    /**
     * Vista: detalle de una gestión.
     */
    public function show(Gestion $gestion)
    {
        return view('gestiones.show', compact('gestion'));
    }

    /**
     * Vista: edición (no publica/cierra).
     */
    public function edit(Gestion $gestion)
    {
        return view('gestiones.edit', compact('gestion'));
    }

    /**
     * Actualiza gestión y (si se envían) sincroniza feriados.
     * Si viene 'feriados', se REEMPLAZA el set completo de feriados de la gestión.
     */
    public function update(Request $request, Gestion $gestion)
    {
        $data = $request->validate([
            'nombre'                   => ['sometimes','string','max:50', Rule::unique('gestiones','nombre')->ignore($gestion->id_gestion, 'id_gestion')],
            'fecha_inicio'             => ['sometimes','date'],
            'fecha_fin'                => ['sometimes','date','after:fecha_inicio'],
            'feriados'                 => ['sometimes','array'],
            'feriados.*.fecha'         => ['required_with:feriados','date'],
            'feriados.*.descripcion'   => ['nullable','string','max:120'],
        ]);

        DB::transaction(function () use ($gestion, $data, $request) {
            $original = $gestion->only(['nombre','fecha_inicio','fecha_fin']);

            // Aplicar cambios de cabecera
            $gestion->fill($data)->save();

            // Si envían feriados, validar rango actualizado y sincronizar
            if (array_key_exists('feriados', $data)) {
                $fi = $gestion->fecha_inicio;
                $ff = $gestion->fecha_fin;

                foreach ($data['feriados'] ?? [] as $f) {
                    if (isset($f['fecha']) && ($f['fecha'] < $fi || $f['fecha'] > $ff)) {
                        abort(422, "El feriado {$f['fecha']} está fuera del rango de la gestión ({$fi} → {$ff}).");
                    }
                }

                // Reemplazar set completo
                Feriado::where('id_gestion', $gestion->id_gestion)->delete();

                if (!empty($data['feriados'])) {
                    $rows = [];
                    foreach ($data['feriados'] as $f) {
                        $rows[] = [
                            'id_gestion'  => $gestion->id_gestion,
                            'fecha'       => $f['fecha'],
                            'descripcion' => $f['descripcion'] ?? null,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ];
                    }
                    Feriado::insert($rows);
                }
            }

            // Bitácora (con antes/después)
            $this->logBitacora($request, [
                'accion'          => 'EDITAR_GESTION',
                'modulo'          => 'GESTIONES',
                'tabla_afectada'  => 'gestiones',
                'registro_id'     => $gestion->id_gestion,
                'descripcion'     => "Edición de gestión {$gestion->nombre}",
                'id_gestion'      => $gestion->id_gestion,
                'cambios_antes'   => $original,
                'cambios_despues' => $gestion->only(['nombre','fecha_inicio','fecha_fin']),
                'metadata'        => [
                    'feriados' => $data['feriados'] ?? null,
                ],
            ]);
        });

        return redirect()
            ->route('gestiones.show', $gestion)
            ->with('status', 'Gestión actualizada.');
    }
}
