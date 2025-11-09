<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia Carrera</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #059669; margin-bottom: 10px; }
        h2 { text-align: center; color: #475569; font-size: 16px; margin-top: 5px; }
        .info { margin: 20px 0; padding: 10px; background-color: #d1fae5; border-left: 4px solid #059669; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #059669; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Reporte de Asistencia por Carrera</h1>
    <h2>{{ $carrera->nombre }}</h2>
    
    <div class="info">
        <p><strong>Facultad:</strong> {{ $carrera->facultad->nombre ?? 'N/A' }}</p>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($validated['fecha_inicio'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($validated['fecha_fin'])->format('d/m/Y') }}</p>
        <p><strong>Total de Docentes:</strong> {{ $resumenDocentes->count() }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($resumenDocentes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Total Clases</th>
                    <th>Presentes</th>
                    <th>Faltas</th>
                    <th>% Puntualidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenDocentes as $resumen)
                    <tr>
                        <td>{{ $resumen['docente'] }}</td>
                        <td style="text-align: center;">{{ $resumen['total'] }}</td>
                        <td style="text-align: center;">{{ $resumen['presentes'] }}</td>
                        <td style="text-align: center;">{{ $resumen['faltas'] }}</td>
                        <td style="text-align: center;"><strong>{{ $resumen['porcentaje'] }}%</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px; padding: 15px; background-color: #fef3c7; border-left: 4px solid #f59e0b;">
            <h3 style="margin-top: 0; color: #92400e;">Resumen General</h3>
            <p><strong>Total de Clases:</strong> {{ $resumenDocentes->sum('total') }}</p>
            <p><strong>Total Presentes:</strong> {{ $resumenDocentes->sum('presentes') }}</p>
            <p><strong>Total Faltas:</strong> {{ $resumenDocentes->sum('faltas') }}</p>
            <p><strong>Promedio de Puntualidad:</strong> {{ round($resumenDocentes->avg('porcentaje'), 2) }}%</p>
        </div>
    @else
        <p style="text-align: center; margin-top: 30px; color: #6b7280;">No hay registros de asistencia para esta carrera en el período seleccionado.</p>
    @endif

    <div class="footer">
        <p>Generado por Sistema de Gestión Académica - FicTic</p>
        <p>Este reporte es útil para Consejos de Carrera y evaluaciones de desempeño docente.</p>
    </div>
</body>
</html>
