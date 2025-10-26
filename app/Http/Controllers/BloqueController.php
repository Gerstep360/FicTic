<?php

namespace App\Http\Controllers;

use App\Models\Bloque;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Support\LogsBitacora;

class BloqueController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:configurar_catalogos'])
             ->only(['create','store','edit','update','destroy']);
    }

    /** Listado de bloques (vista) con filtros básicos. */
    public function index(Request $request)
    {
        $request->validate([
            'q'        => ['sometimes','string','max:20'], // busca por etiqueta
            'per_page' => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = Bloque::query()->orderBy('hora_inicio');

        if ($term = $request->get('q')) {
            $like = '%'.mb_strtolower($term).'%';
            $q->whereRaw('LOWER(etiqueta) LIKE ?', [$like]);
        }

        $perPage = (int) $request->get('per_page', 20);
        $bloques = $q->paginate($perPage)->appends($request->query());

        return view('bloques.index', compact('bloques'));
    }

    /** Form crear. */
    public function create()
    {
        return view('bloques.create');
    }

    /** Crear bloque (CU-04). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'hora_inicio' => ['required','date_format:H:i'],
            'hora_fin'    => ['required','date_format:H:i','after:hora_inicio'],
            'etiqueta'    => ['nullable','string','max:20',
                Rule::unique('bloques','etiqueta')->where(fn($q)=>$q),
            ],
        ]);

        // Unicidad por rango exacto (inicio + fin)
        $exists = Bloque::where('hora_inicio', $data['hora_inicio'])
                        ->where('hora_fin', $data['hora_fin'])
                        ->exists();
        if ($exists) {
            return back()->withErrors(['hora_inicio' => 'Ya existe un bloque con el mismo rango de horas.'])
                         ->withInput();
        }

        $bloque = DB::transaction(function () use ($data, $request) {
            $b = Bloque::create($data);

            // Bitácora
            $this->logBitacora($request, [
                'accion'         => 'CREAR_BLOQUE',
                'modulo'         => 'CATALOGOS',
                'tabla_afectada' => 'bloques',
                'registro_id'    => $b->id_bloque,
                'descripcion'    => "Bloque {$b->etiqueta} {$b->hora_inicio}-{$b->hora_fin}",
                'metadata'       => ['payload' => $data],
            ]);

            return $b;
        });

        return redirect()->route('bloques.show', $bloque)
                         ->with('status','Bloque creado correctamente.');
    }

    /** Detalle. */
    public function show(Bloque $bloque)
    {
        return view('bloques.show', compact('bloque'));
    }

    /** Form editar. */
    public function edit(Bloque $bloque)
    {
        return view('bloques.edit', compact('bloque'));
    }

    /** Actualizar bloque. */
    public function update(Request $request, Bloque $bloque)
    {
        $data = $request->validate([
            'hora_inicio' => ['sometimes','date_format:H:i'],
            'hora_fin'    => ['sometimes','date_format:H:i','after:hora_inicio'],
            'etiqueta'    => ['nullable','string','max:20',
                Rule::unique('bloques','etiqueta')->ignore($bloque->id_bloque,'id_bloque'),
            ],
        ]);

        if (isset($data['hora_inicio']) || isset($data['hora_fin'])) {
            $hi = $data['hora_inicio'] ?? $bloque->hora_inicio;
            $hf = $data['hora_fin']    ?? $bloque->hora_fin;

            $exists = Bloque::where('hora_inicio', $hi)
                            ->where('hora_fin', $hf)
                            ->where('id_bloque','!=',$bloque->id_bloque)
                            ->exists();
            if ($exists) {
                return back()->withErrors(['hora_inicio' => 'Ya existe un bloque con el mismo rango de horas.'])
                             ->withInput();
            }
        }

        DB::transaction(function () use ($bloque, $data, $request) {
            $antes = $bloque->only(['hora_inicio','hora_fin','etiqueta']);
            $bloque->fill($data)->save();

            $this->logBitacora($request, [
                'accion'          => 'EDITAR_BLOQUE',
                'modulo'          => 'CATALOGOS',
                'tabla_afectada'  => 'bloques',
                'registro_id'     => $bloque->id_bloque,
                'descripcion'     => "Edición bloque {$bloque->etiqueta}",
                'cambios_antes'   => $antes,
                'cambios_despues' => $bloque->only(['hora_inicio','hora_fin','etiqueta']),
            ]);
        });

        return redirect()->route('bloques.show', $bloque)
                         ->with('status','Bloque actualizado.');
    }

    /** Eliminar bloque. (Opcional) */
    public function destroy(Bloque $bloque)
    {
        $id = $bloque->id_bloque;
        $desc = "{$bloque->etiqueta} {$bloque->hora_inicio}-{$bloque->hora_fin}";
        $bloque->delete();

        $this->logBitacora(request(), [
            'accion'         => 'ELIMINAR_BLOQUE',
            'modulo'         => 'CATALOGOS',
            'tabla_afectada' => 'bloques',
            'registro_id'    => $id,
            'descripcion'    => "Eliminación de bloque $desc",
        ]);

        return redirect()->route('bloques.index')->with('status','Bloque eliminado.');
    }
}
