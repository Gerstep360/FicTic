<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Aula;
use App\Models\Carrera;
use App\Models\HorarioClase;
use App\Models\Asistencia;
use App\Models\Bloque;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HorarioDocenteExport;
use App\Exports\AsistenciaDocenteExport;
use App\Exports\OcupacionAulasExport;
use Carbon\Carbon;

class ReporteController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver_reportes');
    }
    
    /**
     * Vista principal de reportes
     */
    public function index()
    {
        $docentes = User::role('Docente')->orderBy('name')->get();
        $grupos = Grupo::with(['materia', 'gestion'])
                      ->whereHas('gestion', function($q) {
                          $q->where('publicada', 1);
                      })
                      ->orderBy('nombre_grupo')
                      ->get();
        $aulas = Aula::orderBy('codigo')->get();
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        
        return view('reportes.index', compact('docentes', 'grupos', 'aulas', 'carreras'));
    }
    
    /**
     * Reporte de Horario por Docente
     */
    public function horarioDocente(Request $request)
    {
        $validated = $request->validate([
            'id_docente' => 'required|exists:users,id',
            'formato' => 'required|in:pdf,excel',
        ]);
        
        $docente = User::findOrFail($validated['id_docente']);
        
        $horarios = HorarioClase::with(['grupo.materia', 'aula', 'bloque'])
                                ->where('id_docente', $validated['id_docente'])
                                ->whereHas('grupo.gestion', function($q) {
                                    $q->where('publicada', 1);
                                })
                                ->orderBy('dia_semana')
                                ->orderBy('id_bloque')
                                ->get();
        
        // Registrar en bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_reporte',
            'modulo' => 'Reportes',
            'tabla_afectada' => 'horario_clases',
            'descripcion' => "Reporte de horario docente: {$docente->name} - Formato: {$validated['formato']}",
            'exitoso' => true,
            'metadata' => ['tipo_reporte' => 'horario_docente', 'formato' => $validated['formato']],
        ]);
        
        if ($validated['formato'] === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.horario-docente', compact('docente', 'horarios'));
            return $pdf->download("horario_docente_{$docente->id}_" . date('Y-m-d') . ".pdf");
        } else {
            return Excel::download(
                new HorarioDocenteExport($docente, $horarios),
                "horario_docente_{$docente->id}_" . date('Y-m-d') . ".xlsx"
            );
        }
    }
    
    /**
     * Reporte de Horario por Grupo
     */
    public function horarioGrupo(Request $request)
    {
        $validated = $request->validate([
            'id_grupo' => 'required|exists:grupos,id_grupo',
            'formato' => 'required|in:pdf,excel',
        ]);
        
        $grupo = Grupo::with(['materia', 'gestion', 'docente'])->findOrFail($validated['id_grupo']);
        
        $horarios = HorarioClase::with(['docente', 'aula', 'bloque'])
                                ->where('id_grupo', $validated['id_grupo'])
                                ->orderBy('dia_semana')
                                ->orderBy('id_bloque')
                                ->get();
        
        // Registrar en bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_reporte',
            'modulo' => 'Reportes',
            'tabla_afectada' => 'horario_clases',
            'descripcion' => "Reporte de horario grupo: {$grupo->nombre_grupo} - {$grupo->materia->nombre} - Formato: {$validated['formato']}",
            'exitoso' => true,
            'metadata' => ['tipo_reporte' => 'horario_grupo', 'formato' => $validated['formato']],
        ]);
        
        if ($validated['formato'] === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.horario-grupo', compact('grupo', 'horarios'));
            return $pdf->download("horario_grupo_{$grupo->id_grupo}_" . date('Y-m-d') . ".pdf");
        } else {
            // Similar export para grupo
            return response()->json(['message' => 'Excel para grupos en desarrollo']);
        }
    }
    
    /**
     * Reporte de Horario por Aula
     */
    public function horarioAula(Request $request)
    {
        $validated = $request->validate([
            'id_aula' => 'required|exists:aulas,id_aula',
            'formato' => 'required|in:pdf,excel',
        ]);
        
        $aula = Aula::findOrFail($validated['id_aula']);
        
        $horarios = HorarioClase::with(['grupo.materia', 'docente', 'bloque'])
                                ->where('id_aula', $validated['id_aula'])
                                ->whereHas('grupo.gestion', function($q) {
                                    $q->where('publicada', 1);
                                })
                                ->orderBy('dia_semana')
                                ->orderBy('id_bloque')
                                ->get();
        
        // Registrar en bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_reporte',
            'modulo' => 'Reportes',
            'tabla_afectada' => 'horario_clases',
            'descripcion' => "Reporte de horario aula: {$aula->codigo} - Formato: {$validated['formato']}",
            'exitoso' => true,
            'metadata' => ['tipo_reporte' => 'horario_aula', 'formato' => $validated['formato']],
        ]);
        
        if ($validated['formato'] === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.horario-aula', compact('aula', 'horarios'));
            return $pdf->download("horario_aula_{$aula->codigo}_" . date('Y-m-d') . ".pdf");
        } else {
            return response()->json(['message' => 'Excel para aulas en desarrollo']);
        }
    }
    
    /**
     * Reporte de Asistencia por Docente
     */
    public function asistenciaDocente(Request $request)
    {
        $validated = $request->validate([
            'id_docente' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'formato' => 'required|in:pdf,excel',
        ]);
        
        $docente = User::findOrFail($validated['id_docente']);
        
        $asistencias = Asistencia::with(['horario.grupo.materia', 'horario.aula', 'horario.bloque'])
                                 ->where('id_docente', $validated['id_docente'])
                                 ->whereBetween('fecha_hora', [
                                     Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                                     Carbon::parse($validated['fecha_fin'])->endOfDay()
                                 ])
                                 ->orderBy('fecha_hora', 'desc')
                                 ->get();
        
        // Calcular estadísticas
        $totalClases = $asistencias->count();
        $presentes = $asistencias->where('estado', 'PRESENTE')->count();
        $faltas = $asistencias->where('estado', 'AUSENTE')->count();
        $justificadas = $asistencias->where('estado', 'JUSTIFICADO')->count();
        $porcentajePuntualidad = $totalClases > 0 ? round(($presentes / $totalClases) * 100, 2) : 0;
        
        $estadisticas = compact('totalClases', 'presentes', 'faltas', 'justificadas', 'porcentajePuntualidad');
        
        // Registrar en bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_reporte',
            'modulo' => 'Reportes',
            'tabla_afectada' => 'asistencias',
            'descripcion' => "Reporte de asistencia docente: {$docente->name} ({$validated['fecha_inicio']} a {$validated['fecha_fin']}) - Formato: {$validated['formato']}",
            'exitoso' => true,
            'metadata' => ['tipo_reporte' => 'asistencia_docente', 'formato' => $validated['formato']],
        ]);
        
        if ($validated['formato'] === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.asistencia-docente', compact('docente', 'asistencias', 'estadisticas', 'validated'));
            return $pdf->download("asistencia_docente_{$docente->id}_" . date('Y-m-d') . ".pdf");
        } else {
            return Excel::download(
                new AsistenciaDocenteExport($docente, $asistencias, $estadisticas, $validated),
                "asistencia_docente_{$docente->id}_" . date('Y-m-d') . ".xlsx"
            );
        }
    }
    
    /**
     * Reporte de Asistencia por Carrera
     */
    public function asistenciaCarrera(Request $request)
    {
        $validated = $request->validate([
            'id_carrera' => 'required|exists:carreras,id_carrera',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'formato' => 'required|in:pdf,excel',
        ]);
        
        $carrera = Carrera::with('facultad')->findOrFail($validated['id_carrera']);
        
        // Obtener docentes de la carrera (via grupos)
        $docentesIds = Grupo::whereIn('id_materia', function($q) use ($validated) {
            $q->select('id_materia')
              ->from('materias')
              ->where('id_carrera', $validated['id_carrera']);
        })->pluck('id_docente')->unique();
        
        $asistencias = Asistencia::with(['docente', 'horario.grupo.materia'])
                                 ->whereIn('id_docente', $docentesIds)
                                 ->whereBetween('fecha_hora', [
                                     Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                                     Carbon::parse($validated['fecha_fin'])->endOfDay()
                                 ])
                                 ->get();
        
        // Estadísticas por docente
        $resumenDocentes = $asistencias->groupBy('id_docente')->map(function($asistenciasDocente) {
            $total = $asistenciasDocente->count();
            $presentes = $asistenciasDocente->where('estado', 'PRESENTE')->count();
            $faltas = $asistenciasDocente->where('estado', 'AUSENTE')->count();
            
            return [
                'docente' => $asistenciasDocente->first()->docente->name,
                'total' => $total,
                'presentes' => $presentes,
                'faltas' => $faltas,
                'porcentaje' => $total > 0 ? round(($presentes / $total) * 100, 2) : 0,
            ];
        });
        
        // Registrar en bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_reporte',
            'modulo' => 'Reportes',
            'tabla_afectada' => 'asistencias',
            'descripcion' => "Reporte de asistencia carrera: {$carrera->nombre} ({$validated['fecha_inicio']} a {$validated['fecha_fin']}) - Formato: {$validated['formato']}",
            'exitoso' => true,
            'metadata' => ['tipo_reporte' => 'asistencia_carrera', 'formato' => $validated['formato']],
        ]);
        
        if ($validated['formato'] === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.asistencia-carrera', compact('carrera', 'resumenDocentes', 'validated'));
            return $pdf->download("asistencia_carrera_{$carrera->id_carrera}_" . date('Y-m-d') . ".pdf");
        } else {
            return response()->json(['message' => 'Excel para asistencia carrera en desarrollo']);
        }
    }
    
    /**
     * Reporte de Ocupación de Aulas
     */
    public function ocupacionAulas(Request $request)
    {
        $validated = $request->validate([
            'formato' => 'required|in:pdf,excel',
        ]);
        
        $aulas = Aula::withCount('horarios')->orderBy('codigo')->get();
        
        $bloques = Bloque::count();
        $diasSemana = 6; // Lunes a Sábado
        $totalSlots = $bloques * $diasSemana;
        
        $ocupacion = $aulas->map(function($aula) use ($totalSlots) {
            $ocupados = $aula->horarios_count;
            $porcentaje = $totalSlots > 0 ? round(($ocupados / $totalSlots) * 100, 2) : 0;
            
            return [
                'codigo' => $aula->codigo,
                'tipo' => $aula->tipo,
                'capacidad' => $aula->capacidad,
                'edificio' => $aula->edificio,
                'slots_ocupados' => $ocupados,
                'total_slots' => $totalSlots,
                'porcentaje' => $porcentaje,
            ];
        });
        
        // Registrar en bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_reporte',
            'modulo' => 'Reportes',
            'tabla_afectada' => 'aulas',
            'descripcion' => "Reporte de ocupación de aulas - Formato: {$validated['formato']}",
            'exitoso' => true,
            'metadata' => ['tipo_reporte' => 'ocupacion_aulas', 'formato' => $validated['formato']],
        ]);
        
        if ($validated['formato'] === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.ocupacion-aulas', compact('ocupacion', 'totalSlots'));
            return $pdf->download("ocupacion_aulas_" . date('Y-m-d') . ".pdf");
        } else {
            return Excel::download(
                new OcupacionAulasExport($ocupacion, $totalSlots),
                "ocupacion_aulas_" . date('Y-m-d') . ".xlsx"
            );
        }
    }
}
