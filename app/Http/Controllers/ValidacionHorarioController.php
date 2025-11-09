<?php

namespace App\Http\Controllers;

use App\Models\ReglaValidacion;
use App\Models\Gestion;
use App\Models\Carrera;
use App\Models\Facultad;
use App\Services\ValidadorHorarios;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;

class ValidacionHorarioController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:validar_conflictos|Admin DTIC'])
            ->except(['index', 'show']);
    }

    /**
     * Panel principal de validación
     */
    public function index()
    {
        $gestiones = Gestion::orderBy('nombre', 'desc')->get();
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        
        return view('validacion-horarios.index', compact('gestiones', 'carreras'));
    }

    /**
     * Ejecutar validación de horarios
     */
    public function validar(Request $request)
    {
        $validated = $request->validate([
            'id_gestion' => ['required', 'exists:gestiones,id_gestion'],
            'id_carrera' => ['nullable', 'exists:carreras,id_carrera'],
        ]);

        try {
            $validador = new ValidadorHorarios(
                $validated['id_gestion'],
                $validated['id_carrera'] ?? null
            );

            $resultado = $validador->validar();

            // Asegurar que el resultado tenga la estructura completa
            $resultado['resumen'] = $resultado['resumen'] ?? [
                'total_conflictos' => 0,
                'total_advertencias' => 0,
                'criticos' => 0,
                'altos' => 0,
                'medios' => 0,
                'bajos' => 0,
            ];

            // Registrar en bitácora
            $this->logBitacora($request, [
                'accion' => 'validar',
                'modulo' => 'Validación de Horarios',
                'tabla_afectada' => 'horario_clases',
                'descripcion' => "Validación ejecutada: {$resultado['resumen']['total_conflictos']} conflictos, {$resultado['resumen']['total_advertencias']} advertencias",
                'id_gestion' => $validated['id_gestion'],
                'exitoso' => true,
            ]);

            return view('validacion-horarios.resultado', [
                'resultado' => $resultado,
                'gestion' => Gestion::find($validated['id_gestion']),
                'carrera' => $validated['id_carrera'] ? Carrera::find($validated['id_carrera']) : null,
            ]);
        } catch (\Exception $e) {
            $this->logBitacora($request, [
                'accion' => 'validar',
                'modulo' => 'Validación de Horarios',
                'tabla_afectada' => 'horario_clases',
                'descripcion' => "Error en validación: " . $e->getMessage(),
                'id_gestion' => $validated['id_gestion'],
                'exitoso' => false,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al validar horarios: ' . $e->getMessage());
        }
    }

    /**
     * Gestión de reglas de validación
     */
    public function reglas()
    {
        $reglas = ReglaValidacion::with(['facultad', 'carrera'])
            ->orderBy('categoria')
            ->orderBy('severidad')
            ->get();

        $facultades = Facultad::orderBy('nombre')->get();
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();

        return view('validacion-horarios.reglas', compact('reglas', 'facultades', 'carreras'));
    }

    /**
     * Crear nueva regla
     */
    public function storeRegla(Request $request)
    {
        $validated = $request->validate([
            'id_facultad' => ['nullable', 'exists:facultades,id_facultad'],
            'id_carrera' => ['nullable', 'exists:carreras,id_carrera'],
            'codigo' => ['required', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', 'in:carga_docente,descanso,tipo_aula,capacidad,continuidad,preferencias,otras'],
            'severidad' => ['required', 'in:critica,alta,media,baja'],
            'activa' => ['boolean'],
            'bloqueante' => ['boolean'],
            'parametros' => ['nullable', 'json'],
        ]);

        $validated['activa'] = $request->boolean('activa', true);
        $validated['bloqueante'] = $request->boolean('bloqueante', false);
        
        if ($request->filled('parametros')) {
            $validated['parametros'] = json_decode($request->parametros, true);
        }

        $regla = ReglaValidacion::create($validated);

        $this->logBitacora($request, [
            'accion' => 'crear',
            'modulo' => 'Reglas de Validación',
            'tabla_afectada' => 'reglas_validacion',
            'registro_id' => $regla->id_regla,
            'descripcion' => "Nueva regla creada: {$regla->nombre} ({$regla->codigo})",
            'exitoso' => true,
        ]);

        return redirect()
            ->route('validacion-horarios.reglas')
            ->with('success', 'Regla creada exitosamente.');
    }

    /**
     * Actualizar regla
     */
    public function updateRegla(Request $request, ReglaValidacion $regla)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'severidad' => ['required', 'in:critica,alta,media,baja'],
            'activa' => ['boolean'],
            'bloqueante' => ['boolean'],
            'parametros' => ['nullable', 'json'],
        ]);

        $validated['activa'] = $request->boolean('activa', $regla->activa);
        $validated['bloqueante'] = $request->boolean('bloqueante', $regla->bloqueante);
        
        if ($request->filled('parametros')) {
            $validated['parametros'] = json_decode($request->parametros, true);
        }

        $regla->update($validated);

        $this->logBitacora($request, [
            'accion' => 'actualizar',
            'modulo' => 'Reglas de Validación',
            'tabla_afectada' => 'reglas_validacion',
            'registro_id' => $regla->id_regla,
            'descripcion' => "Regla actualizada: {$regla->nombre}",
            'exitoso' => true,
        ]);

        return redirect()
            ->route('validacion-horarios.reglas')
            ->with('success', 'Regla actualizada exitosamente.');
    }

    /**
     * Eliminar regla
     */
    public function destroyRegla(Request $request, ReglaValidacion $regla)
    {
        $id = $regla->id_regla;
        $nombre = $regla->nombre;
        
        $regla->delete();

        $this->logBitacora($request, [
            'accion' => 'eliminar',
            'modulo' => 'Reglas de Validación',
            'tabla_afectada' => 'reglas_validacion',
            'registro_id' => $id,
            'descripcion' => "Regla eliminada: {$nombre}",
            'exitoso' => true,
        ]);

        return redirect()
            ->route('validacion-horarios.reglas')
            ->with('success', 'Regla eliminada exitosamente.');
    }

    /**
     * Activar/Desactivar regla rápidamente
     */
    public function toggleRegla(Request $request, ReglaValidacion $regla)
    {
        $regla->update(['activa' => !$regla->activa]);

        $estado = $regla->activa ? 'activada' : 'desactivada';

        $this->logBitacora($request, [
            'accion' => 'toggle',
            'modulo' => 'Reglas de Validación',
            'tabla_afectada' => 'reglas_validacion',
            'registro_id' => $regla->id_regla,
            'descripcion' => "Regla {$estado}: {$regla->nombre}",
            'exitoso' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', "Regla {$estado} exitosamente.");
    }
}
