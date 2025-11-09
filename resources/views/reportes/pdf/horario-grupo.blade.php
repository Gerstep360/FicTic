<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario Grupo</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #1e40af; margin-bottom: 10px; }
        h2 { text-align: center; color: #475569; font-size: 16px; margin-top: 5px; }
        .info { margin: 20px 0; padding: 10px; background-color: #e0e7ff; border-left: 4px solid #1e40af; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #1e40af; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Horario del Grupo</h1>
    <h2>{{ $grupo->materia->nombre ?? 'N/A' }} - {{ $grupo->nombre_grupo }}</h2>
    
    <div class="info">
        <p><strong>Materia:</strong> {{ $grupo->materia->nombre ?? 'N/A' }}</p>
        <p><strong>Código:</strong> {{ $grupo->materia->codigo ?? 'N/A' }}</p>
        <p><strong>Docente:</strong> {{ $grupo->docente->name ?? 'No asignado' }}</p>
        <p><strong>Turno:</strong> {{ $grupo->turno }}</p>
        <p><strong>Modalidad:</strong> {{ $grupo->modalidad }}</p>
        <p><strong>Gestión:</strong> {{ $grupo->gestion->nombre ?? 'N/A' }}</p>
        <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($horarios->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Horario</th>
                    <th>Aula</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                @endphp
                @foreach($horarios as $horario)
                    <tr>
                        <td>{{ $dias[$horario->dia_semana] ?? 'N/A' }}</td>
                        <td>{{ $horario->bloque->hora_inicio }} - {{ $horario->bloque->hora_fin }}</td>
                        <td>{{ $horario->aula->codigo ?? 'N/A' }}</td>
                        <td>{{ $horario->docente->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; margin-top: 30px; color: #6b7280;">Este grupo no tiene horarios asignados.</p>
    @endif

    <div class="footer">
        <p>Generado por Sistema de Gestión Académica - FicTic</p>
    </div>
</body>
</html>
