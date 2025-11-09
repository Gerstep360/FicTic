<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia Docente</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { text-align: center; color: #059669; margin-bottom: 10px; }
        h2 { text-align: center; color: #475569; font-size: 16px; margin-top: 5px; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; }
        .stat-box { border: 2px solid #059669; border-radius: 8px; padding: 15px; text-align: center; width: 18%; }
        .stat-box h3 { margin: 0; font-size: 24px; color: #059669; }
        .stat-box p { margin: 5px 0 0 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #059669; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { border: 1px solid #ddd; padding: 6px; font-size: 10px; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 9px; font-weight: bold; }
        .badge-presente { background-color: #d1fae5; color: #065f46; }
        .badge-ausente { background-color: #fee2e2; color: #991b1b; }
        .badge-justificado { background-color: #fef3c7; color: #92400e; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Reporte de Asistencia Docente</h1>
    <h2>{{ $docente->name }}</h2>
    <p style="text-align: center;"><strong>Período:</strong> {{ \Carbon\Carbon::parse($validated['fecha_inicio'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($validated['fecha_fin'])->format('d/m/Y') }}</p>
    <p style="text-align: center;"><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    <div class="stats">
        <div class="stat-box">
            <h3>{{ $estadisticas['totalClases'] }}</h3>
            <p>Total Clases</p>
        </div>
        <div class="stat-box">
            <h3>{{ $estadisticas['presentes'] }}</h3>
            <p>Presentes</p>
        </div>
        <div class="stat-box">
            <h3>{{ $estadisticas['faltas'] }}</h3>
            <p>Faltas</p>
        </div>
        <div class="stat-box">
            <h3>{{ $estadisticas['justificadas'] }}</h3>
            <p>Justificadas</p>
        </div>
        <div class="stat-box">
            <h3>{{ $estadisticas['porcentajePuntualidad'] }}%</h3>
            <p>Puntualidad</p>
        </div>
    </div>

    @if($asistencias->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Materia</th>
                    <th>Aula</th>
                    <th>Tipo Marca</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asistencias as $asistencia)
                    <tr>
                        <td>{{ $asistencia->fecha_hora->format('d/m/Y H:i') }}</td>
                        <td>{{ $asistencia->horario->grupo->materia->nombre ?? 'N/A' }}</td>
                        <td>{{ $asistencia->horario->aula->codigo ?? 'N/A' }}</td>
                        <td>{{ $asistencia->tipo_marca }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($asistencia->estado) }}">
                                {{ $asistencia->estado }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; margin-top: 30px; color: #6b7280;">No hay registros de asistencia en el período seleccionado.</p>
    @endif

    <div class="footer">
        <p>Generado por Sistema de Gestión Académica - FicTic</p>
    </div>
</body>
</html>
