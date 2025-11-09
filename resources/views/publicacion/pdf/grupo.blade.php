<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario - {{ $grupo->codigo_grupo }}</title>
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
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #059669;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }
        .info {
            margin-bottom: 15px;
            background-color: #f0fdf4;
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
            background-color: #059669;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 9px;
            border: 1px solid #047857;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
            font-size: 8px;
        }
        td.dia {
            background-color: #d1fae5;
            font-weight: bold;
            text-align: center;
            width: 12%;
            color: #065f46;
        }
        td.horario {
            width: 88%;
        }
        .clase {
            background-color: #d1fae5;
            border-left: 3px solid #10b981;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 3px;
        }
        .clase-docente {
            font-weight: bold;
            color: #059669;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .clase-info {
            color: #475569;
            font-size: 7px;
            line-height: 1.3;
        }
        .seccion {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .seccion-title {
            font-size: 11px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .docente-item, .aula-item {
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
        .grupo-banner {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 12px;
        }
        .grid-2 {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .grid-col {
            display: table-cell;
            width: 50%;
            padding: 0 5px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>HORARIO DE CLASES - GRUPO</h1>
        <h2>{{ $grupo->materia->carrera->facultad->nombre_facultad ?? '' }}</h2>
    </div>

    <div class="grupo-banner">
        {{ $grupo->materia->nombre_materia ?? 'N/A' }} - GRUPO {{ $grupo->codigo_grupo }}
    </div>

    <div class="info">
        <div class="info-row">
            <div class="info-label">Carrera:</div>
            <div class="info-value">{{ $grupo->materia->carrera->nombre_carrera ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Cupo:</div>
            <div class="info-value">{{ $grupo->cupo }} estudiantes</div>
        </div>
        <div class="info-row">
            <div class="info-label">Turno:</div>
            <div class="info-value">{{ ucfirst($grupo->turno) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Modalidad:</div>
            <div class="info-value">{{ ucfirst($grupo->modalidad) }}</div>
        </div>
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
                                    <div class="clase-docente">
                                        {{ $horario->docente->name ?? 'Sin asignar' }}
                                    </div>
                                    <div class="clase-info">
                                        <strong>Horario:</strong> 
                                        {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($horario->bloque->hora_fin)->format('H:i') }} 
                                        ({{ $horario->bloque->duracion_horas ?? 0 }}h)<br>
                                        <strong>Aula:</strong> {{ $horario->aula->codigo_aula ?? 'N/A' }} - 
                                        {{ $horario->aula->tipo_aula ?? '' }} 
                                        (Capacidad: {{ $horario->aula->capacidad ?? 'N/A' }})
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

    <div class="grid-2">
        <div class="grid-col">
            <div class="seccion">
                <div class="seccion-title">DOCENTES ASIGNADOS</div>
                @foreach($horarios->pluck('docente')->unique('id') as $docente)
                    @if($docente)
                        <div class="docente-item">
                            <strong>{{ $docente->name }}</strong><br>
                            {{ $docente->email }}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="grid-col">
            <div class="seccion">
                <div class="seccion-title">AULAS UTILIZADAS</div>
                @foreach($horarios->pluck('aula')->unique('id_aula') as $aula)
                    @if($aula)
                        <div class="aula-item">
                            <strong>{{ $aula->codigo_aula }}</strong><br>
                            {{ $aula->tipo_aula }} - Capacidad: {{ $aula->capacidad }}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="footer">
        Sistema de Gestión Académica - Documento generado el {{ now()->format('d/m/Y H:i') }}<br>
        Este horario es oficial y ha sido aprobado por las autoridades correspondientes
    </div>

</body>
</html>
