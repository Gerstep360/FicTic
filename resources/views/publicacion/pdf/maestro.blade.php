<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Maestro Oferta - {{ $gestion->nombre }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            color: #5b21b6;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 12px;
            color: #64748b;
            font-weight: normal;
        }
        .publicacion-info {
            background-color: #f0fdf4;
            border: 2px solid #10b981;
            padding: 6px;
            margin-bottom: 12px;
            text-align: center;
            border-radius: 3px;
        }
        .publicacion-info strong {
            color: #047857;
        }
        .estadisticas {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 6px;
            text-align: center;
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
            border: 2px solid #7c3aed;
            margin: 0 3px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #5b21b6;
            margin-bottom: 2px;
        }
        .stat-label {
            font-size: 7px;
            color: #6b21a8;
        }
        .carrera-section {
            page-break-inside: avoid;
            margin-bottom: 15px;
        }
        .carrera-header {
            background: linear-gradient(90deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px 3px 0 0;
        }
        .carrera-subheader {
            background-color: #ede9fe;
            color: #5b21b6;
            padding: 4px 10px;
            font-size: 8px;
            border-bottom: 1px solid #7c3aed;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        thead {
            background-color: #5b21b6;
            color: white;
        }
        th {
            padding: 5px 3px;
            text-align: left;
            font-size: 7px;
            border: 1px solid #4c1d95;
        }
        th.center {
            text-align: center;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 4px 3px;
            vertical-align: top;
            font-size: 7px;
            line-height: 1.3;
        }
        td.center {
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        tr:hover {
            background-color: #f1f5f9;
        }
        .materia-nombre {
            font-weight: bold;
            color: #1e293b;
        }
        .horario-detalle {
            font-size: 6px;
            color: #475569;
            line-height: 1.2;
        }
        .horario-dia {
            font-weight: bold;
            color: #7c3aed;
        }
        .turno-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6px;
            font-weight: bold;
        }
        .turno-mañana {
            background-color: #fef3c7;
            color: #92400e;
        }
        .turno-tarde {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .turno-noche {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 6px;
            color: #94a3b8;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
            page-break-inside: avoid;
        }
        .no-grupos {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>MAESTRO DE OFERTA ACADÉMICA</h1>
        <h2>{{ $gestion->nombre }}</h2>
        <div style="font-size: 9px; color: #64748b; margin-top: 3px;">
            Periodo: {{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}
        </div>
    </div>

    @if($gestion->publicada)
        <div class="publicacion-info">
            <strong>✓ DOCUMENTO OFICIAL</strong> - 
            Publicado el {{ $gestion->fecha_publicacion?->format('d/m/Y H:i') }} 
            por {{ $gestion->usuarioPublicador->name ?? 'N/A' }}
            @if($gestion->nota_publicacion)
                <br><em>{{ $gestion->nota_publicacion }}</em>
            @endif
        </div>
    @endif

    <div class="estadisticas">
        <div class="stat-card" style="margin-right: 2px;">
            <div class="stat-value">{{ $carreras->count() }}</div>
            <div class="stat-label">CARRERAS</div>
        </div>
        <div class="stat-card" style="margin: 0 1px;">
            <div class="stat-value">
                {{ $carreras->sum(function($c) { return $c->grupos->pluck('materia')->unique('id_materia')->count(); }) }}
            </div>
            <div class="stat-label">MATERIAS</div>
        </div>
        <div class="stat-card" style="margin: 0 1px;">
            <div class="stat-value">
                {{ $carreras->sum(function($c) { return $c->grupos->count(); }) }}
            </div>
            <div class="stat-label">GRUPOS</div>
        </div>
        <div class="stat-card" style="margin-left: 2px;">
            <div class="stat-value">
                {{ $carreras->flatMap(function($c) { 
                    return $c->grupos->flatMap(function($g) { 
                        return $g->horarios->pluck('docente'); 
                    }); 
                })->unique('id')->count() }}
            </div>
            <div class="stat-label">DOCENTES</div>
        </div>
    </div>

    @foreach($carreras as $carrera)
        <div class="carrera-section">
            <div class="carrera-header">
                {{ $carrera->nombre }}
            </div>
            <div class="carrera-subheader">
                {{ $carrera->grupos->count() }} grupos - 
                {{ $carrera->grupos->pluck('materia')->unique('id_materia')->count() }} materias diferentes - 
                Cupo total: {{ $carrera->grupos->sum('cupo') }} estudiantes
            </div>

            @if($carrera->grupos->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">MATERIA</th>
                            <th style="width: 6%;" class="center">GRUPO</th>
                            <th style="width: 15%;">DOCENTE(S)</th>
                            <th style="width: 5%;" class="center">CUPO</th>
                            <th style="width: 6%;" class="center">TURNO</th>
                            <th style="width: 5%;" class="center">HORAS</th>
                            <th style="width: 43%;">HORARIOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carrera->grupos->sortBy('materia.nombre') as $grupo)
                            <tr>
                                <td>
                                    <div class="materia-nombre">
                                        {{ $grupo->materia->nombre ?? 'N/A' }}
                                    </div>
                                    <div style="font-size: 6px; color: #64748b;">
                                        {{ $grupo->modalidad ? ucfirst($grupo->modalidad) : '' }}
                                    </div>
                                </td>
                                <td class="center">
                                    <strong>{{ $grupo->nombre_grupo }}</strong>
                                </td>
                                <td>
                                    @php
                                        $docentes = $grupo->horarios->pluck('docente')->unique('id')->filter();
                                    @endphp
                                    @if($docentes->isNotEmpty())
                                        @foreach($docentes as $docente)
                                            <div style="margin-bottom: 2px;">{{ $docente->name }}</div>
                                        @endforeach
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="center">
                                    <strong>{{ $grupo->cupo }}</strong>
                                </td>
                                <td class="center">
                                    <span class="turno-badge turno-{{ $grupo->turno }}">
                                        {{ ucfirst($grupo->turno) }}
                                    </span>
                                </td>
                                <td class="center">
                                    <strong>{{ $grupo->horarios->sum(function($h) { return $h->bloque->duracion_horas ?? 0; }) }}</strong>
                                </td>
                                <td>
                                    @if($grupo->horarios->isNotEmpty())
                                        <div class="horario-detalle">
                                            @php
                                                $horariosPorDia = $grupo->horarios->groupBy('dia_semana')->sortKeys();
                                            @endphp
                                            @foreach($horariosPorDia as $dia => $hrs)
                                                <div style="margin-bottom: 2px;">
                                                    <span class="horario-dia">{{ $hrs->first()->dia_nombre }}:</span>
                                                    @foreach($hrs->sortBy(fn($h) => $h->bloque->hora_inicio) as $h)
                                                        {{ \Carbon\Carbon::parse($h->bloque->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($h->bloque->hora_fin)->format('H:i') }}
                                                        <span style="color: #7c3aed;">({{ $h->aula->codigo ?? 'N/A' }})</span>
                                                        @if(!$loop->last), @endif
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Sin horarios</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-grupos">
                    No hay grupos registrados para esta carrera
                </div>
            @endif
        </div>
    @endforeach

    @if($carreras->isEmpty())
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            <div style="font-size: 48px; margin-bottom: 10px;">📋</div>
            <div style="font-size: 12px;">No hay oferta académica registrada para esta gestión</div>
        </div>
    @endif

    <div class="footer">
        Sistema de Gestión Académica - Maestro de Oferta Oficial<br>
        Documento generado el {{ now()->format('d/m/Y H:i') }} - 
        Este documento contiene la oferta académica oficial aprobada por las autoridades correspondientes
    </div>

</body>
</html>
