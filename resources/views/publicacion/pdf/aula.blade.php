<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ocupación - {{ $aula->codigo_aula }}</title>
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
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #ea580c;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }
        .info {
            margin-bottom: 15px;
            background-color: #fff7ed;
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
            width: 25%;
            color: #475569;
        }
        .info-value {
            display: table-cell;
            width: 75%;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #ea580c;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 9px;
            border: 1px solid #c2410c;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
            font-size: 8px;
        }
        td.dia {
            background-color: #fed7aa;
            font-weight: bold;
            text-align: center;
            width: 12%;
            color: #9a3412;
        }
        td.horario {
            width: 88%;
        }
        .clase {
            background-color: #ffedd5;
            border-left: 3px solid #f97316;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 3px;
        }
        .clase-materia {
            font-weight: bold;
            color: #ea580c;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .clase-info {
            color: #475569;
            font-size: 7px;
            line-height: 1.3;
        }
        .estadisticas {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-card {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            background-color: #fff7ed;
            border: 2px solid #f97316;
            margin: 0 5px;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #ea580c;
            margin-bottom: 3px;
        }
        .stat-label {
            font-size: 8px;
            color: #9a3412;
        }
        .listado {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            border-radius: 5px;
        }
        .listado-title {
            font-size: 11px;
            font-weight: bold;
            color: #ea580c;
            margin-bottom: 8px;
        }
        .clase-row {
            background-color: white;
            border-left: 3px solid #f97316;
            padding: 5px;
            margin-bottom: 3px;
            font-size: 7px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }
        .aula-banner {
            background-color: #fed7aa;
            border: 2px solid #f97316;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            color: #9a3412;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>HORARIO DE OCUPACIÓN - AULA</h1>
        <h2>{{ $gestion->nombre }}</h2>
    </div>

    <div class="aula-banner">
        AULA {{ $aula->codigo_aula }} - {{ $aula->tipo_aula }}
    </div>

    <div class="info">
        <div class="info-row">
            <div class="info-label">Capacidad:</div>
            <div class="info-value">{{ $aula->capacidad }} estudiantes</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tipo:</div>
            <div class="info-value">{{ $aula->tipo_aula }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Periodo:</div>
            <div class="info-value">
                {{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <div class="estadisticas">
        <div class="stat-card" style="margin-right: 5px;">
            <div class="stat-value">{{ $horarios->count() }}</div>
            <div class="stat-label">CLASES PROGRAMADAS</div>
        </div>
        <div class="stat-card" style="margin: 0 2.5px;">
            <div class="stat-value">
                {{ $horarios->sum(function($h) { return $h->bloque->duracion_horas ?? 0; }) }}
            </div>
            <div class="stat-label">HORAS OCUPADAS/SEMANA</div>
        </div>
        <div class="stat-card" style="margin-left: 5px;">
            <div class="stat-value">
                {{ $horarios->pluck('grupo.materia.carrera')->unique('id_carrera')->count() }}
            </div>
            <div class="stat-label">CARRERAS ATENDIDAS</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">DÍA</th>
                <th style="width: 88%;">HORARIOS DE OCUPACIÓN</th>
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
                                        <strong>Docente:</strong> {{ $horario->docente->name ?? 'Sin asignar' }}<br>
                                        <strong>Carrera:</strong> {{ $horario->grupo->materia->carrera->nombre_carrera ?? 'N/A' }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align: center; color: #94a3b8; padding: 10px;">Libre</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="listado">
        <div class="listado-title">LISTADO COMPLETO DE CLASES (ORDENADO CRONOLÓGICAMENTE)</div>
        @php
            $horariosOrdenados = $horarios->sortBy([
                ['dia', 'asc'],
                fn($h) => $h->bloque->hora_inicio
            ]);
            $diasMap = [
                'lunes' => 1, 'martes' => 2, 'miercoles' => 3,
                'jueves' => 4, 'viernes' => 5, 'sabado' => 6
            ];
            $horariosOrdenados = $horarios->sort(function($a, $b) use ($diasMap) {
                $diaA = $diasMap[$a->dia] ?? 99;
                $diaB = $diasMap[$b->dia] ?? 99;
                if ($diaA != $diaB) return $diaA - $diaB;
                return strcmp($a->bloque->hora_inicio, $b->bloque->hora_inicio);
            });
        @endphp
        @foreach($horariosOrdenados as $horario)
            <div class="clase-row">
                <strong>{{ ucfirst($horario->dia) }}</strong> 
                {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($horario->bloque->hora_fin)->format('H:i') }} | 
                <strong>{{ $horario->grupo->materia->nombre_materia ?? 'N/A' }}</strong> 
                ({{ $horario->grupo->codigo_grupo ?? 'N/A' }}) | 
                Docente: {{ $horario->docente->name ?? 'Sin asignar' }} | 
                {{ $horario->grupo->materia->carrera->nombre_carrera ?? 'N/A' }}
            </div>
        @endforeach
    </div>

    <div class="footer">
        Sistema de Gestión Académica - Documento generado el {{ now()->format('d/m/Y H:i') }}<br>
        Reporte oficial de ocupación de aulas
    </div>

</body>
</html>
