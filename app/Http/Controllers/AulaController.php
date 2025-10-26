<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\LogsBitacora;

class AulaController extends Controller
{
    use LogsBitacora;

    // Catálogo de tipos permitidos
    private const TIPOS = ['Teórica','Laboratorio','Computación','Auditorio'];

    public function __construct()
    {
        $this->middleware(['auth']);
        // Permisos granulares o paraguas "gestionar_aulas"
        $this->middleware(['permission:ver_aulas|gestionar_aulas'])->only(['index']);
        $this->middleware(['permission:crear_aulas|gestionar_aulas'])->only(['create','store']);
        $this->middleware(['permission:editar_aulas|gestionar_aulas'])->only(['edit','update']);
        $this->middleware(['permission:eliminar_aulas|gestionar_aulas'])->only(['destroy']);
        $this->middleware(['permission:restaurar_aulas|gestionar_aulas'])->only(['restore']);
    }

    // Listado + filtros
    public function index(Request $request)
    {
        $request->validate([
            'q'          => ['sometimes','string','max:100'],
            'tipo'       => ['sometimes','string', Rule::in(self::TIPOS)],
            'cap_min'    => ['sometimes','integer','min:0','max:1000'],
            'cap_max'    => ['sometimes','integer','min:0','max:5000'],
            'with_trash' => ['sometimes','boolean'],
            'per_page'   => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = Aula::query();

        if ($term = $request->q) {
            $like = '%'.mb_strtolower($term).'%';
            $q->where(function($w) use ($like) {
                $w->whereRaw('LOWER(codigo) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(edificio) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(tipo) LIKE ?', [$like]);
            });
        }

        if ($tipo = $request->tipo)    $q->where('tipo', $tipo);
        if ($min  = $request->cap_min) $q->where('capacidad', '>=', $min);
        if ($max  = $request->cap_max) $q->where('capacidad', '<=', $max);

        if ($request->boolean('with_trash')) $q->withTrashed();

        $aulas = $q->orderBy('codigo')->paginate((int)$request->get('per_page', 20))
                   ->appends($request->query());

        // Si quieres JSON en API:
        if ($request->wantsJson()) return response()->json($aulas);

        return view('aulas.index', [
            'aulas' => $aulas,
            'tipos' => self::TIPOS,
        ]); // Crea tu blade con tu estilo (opcional)
    }

    public function create()
    {
        return view('aulas.create', ['tipos' => self::TIPOS]); // opcional
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'    => ['required','string','max:20','unique:aulas,codigo'],
            'tipo'      => ['required','string', Rule::in(self::TIPOS)],
            'capacidad' => ['nullable','integer','min:1','max:5000'],
            'edificio'  => ['nullable','string','max:50'],
        ]);

        $aula = Aula::create($data);

        $this->logBitacora($request, [
            'accion' => 'CREAR_AULA',
            'modulo' => 'INFRAESTRUCTURA',
            'tabla_afectada' => 'aulas',
            'registro_id' => $aula->id_aula,
            'descripcion' => "Creación de aula {$aula->codigo}",
            'metadata' => ['payload' => $data],
        ]);

        return redirect()->route('aulas.index')->with('status','Aula creada');
    }

    public function edit(Aula $aula)
    {
        return view('aulas.edit', ['aula'=>$aula, 'tipos'=>self::TIPOS]); // opcional
    }

    public function update(Request $request, Aula $aula)
    {
        $data = $request->validate([
            'codigo'    => ['required','string','max:20', Rule::unique('aulas','codigo')->ignore($aula->id_aula, 'id_aula')],
            'tipo'      => ['required','string', Rule::in(self::TIPOS)],
            'capacidad' => ['nullable','integer','min:1','max:5000'],
            'edificio'  => ['nullable','string','max:50'],
        ]);

        $aula->update($data);

        $this->logBitacora($request, [
            'accion' => 'ACTUALIZAR_AULA',
            'modulo' => 'INFRAESTRUCTURA',
            'tabla_afectada' => 'aulas',
            'registro_id' => $aula->id_aula,
            'descripcion' => "Actualización de aula {$aula->codigo}",
            'metadata' => ['payload' => $data],
        ]);

        return redirect()->route('aulas.index')->with('status','Aula actualizada');
    }

    public function destroy(Request $request, Aula $aula)
    {
        $aula->delete();

        $this->logBitacora($request, [
            'accion' => 'ELIMINAR_AULA',
            'modulo' => 'INFRAESTRUCTURA',
            'tabla_afectada' => 'aulas',
            'registro_id' => $aula->id_aula,
            'descripcion' => "Baja lógica de aula {$aula->codigo}",
        ]);

        return back()->with('status','Aula eliminada');
    }

    public function restore(Request $request, $id_aula)
    {
        $aula = Aula::onlyTrashed()->where('id_aula', $id_aula)->firstOrFail();
        $aula->restore();

        $this->logBitacora($request, [
            'accion' => 'RESTAURAR_AULA',
            'modulo' => 'INFRAESTRUCTURA',
            'tabla_afectada' => 'aulas',
            'registro_id' => $aula->id_aula,
            'descripcion' => "Restauración de aula {$aula->codigo}",
        ]);

        return back()->with('status','Aula restaurada');
    }
}
