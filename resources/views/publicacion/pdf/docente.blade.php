<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario - {{ $docente->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }
        .info {
            margin-bottom: 15px;
            background-color: #f1f5f9;
            padding: 10px;
            border-radius: 5px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 20%;
            color: #475569;
        }
        .info-value {
            display: table-cell;
            width: 80%;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #1e40af;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 9px;
            border: 1px solid #1e3a8a;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
            font-size: 8px;
        }
        td.dia {
            background-color: #e0e7ff;
            font-weight: bold;
            text-align: center;
            width: 12%;
            color: #3730a3;
        }
        td.horario {
            width: 88%;
        }
        .clase {
            background-color: #dbeafe;
            border-left: 3px solid #2563eb;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 3px;
        }
        .clase-materia {
            font-weight: bold;
            color: #1e40af;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .clase-info {
            color: #475569;
            font-size: 7px;
            line-height: 1.3;
        }
        .resumen {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            border-radius: 5px;
        }
        .resumen-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
        }
        .materia-item {
            background-color: white;
            border-left: 3px solid #10b981;
            padding: 5px;
            margin-bottom: 5px;
            font-size: 8px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }
        .total-horas {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>HORARIO DE CLASES</h1>
        <h2>{{ $gestion->nombre }}</h2>
    </div>

    <div class="info">
        <div class="info-row">
            <div class="info-label">Docente:</div>
            <div class="info-value">{{ $docente->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Correo:</div>
            <div class="info-value">{{ $docente->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Periodo:</div>
            <div class="info-value">
                {{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <div class="total-horas">
        CARGA TOTAL: {{ $horarios->sum(function($h) { return $h->bloque->duracion_horas ?? 0; }) }} HORAS SEMANALES
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">DÍA</th>
                <th style="width: 88%;">HORARIOS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                $nombresDias = ['LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
            @endphp
            @foreach($dias as $index => $dia)
                @php
                    $horariosDia = $horarios->where('dia', $dia)->sortBy(function($h) {
                        return $h->bloque->hora_inicio;
                    });
                @endphp
                <tr>
                    <td class="dia">{{ $nombresDias[$index] }}</td>
                    <td class="horario">
                        @if($horariosDia->isNotEmpty())
                            @foreach($horariosDia as $horario)
                                <div class="clase">
                                    <div class="clase-materia">
                                        {{ $horario->grupo->materia->nombre_materia ?? 'N/A' }}
                                    </div>
                                    <div class="clase-info">
                                        <strong>Horario:</strong> 
                                        {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($horario->bloque->hora_fin)->format('H:i') }} 
                                        ({{ $horario->bloque->duracion_horas ?? 0 }}h)<br>
                                        <strong>Grupo:</strong> {{ $horario->grupo->codigo_grupo ?? 'N/A' }}<br>
                                        <strong>Aula:</strong> {{ $horario->aula->codigo_aula ?? 'N/A' }}<br>
                                        <strong>Carrera:</strong> {{ $horario->grupo->materia->carrera->nombre_carrera ?? 'N/A' }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #94a3b8; padding: 10px;">Sin clases</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="resumen">
        <div class="resumen-title">MATERIAS ASIGNADAS</div>
        @foreach($horarios->groupBy('grupo.materia.nombre_materia') as $materia => $clases)
            <div class="materia-item">
                <strong>{{ $materia }}</strong> - 
                Grupo: {{ $clases->first()->grupo->codigo_grupo ?? 'N/A' }} - 
                {{ $clases->sum(function($h) { return $h->bloque->duracion_horas ?? 0; }) }} horas semanales
            </div>
        @endforeach
    </div>

    <div class="footer">
        Sistema de Gestión Académica - Documento generado el {{ now()->format('d/m/Y H:i') }}<br>
        Este horario es oficial y ha sido aprobado por las autoridades correspondientes
    </div>

</body>
</html>
