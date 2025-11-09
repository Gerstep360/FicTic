<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario - {{ $generacionHorario->gestion->nombre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #ffffff;
        }
        
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .header .info-grid {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        
        .header .info-item {
            display: table-cell;
            padding: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin: 0 5px;
        }
        
        .header .info-label {
            font-size: 9px;
            opacity: 0.7;
            display: block;
            margin-bottom: 3px;
        }
        
        .header .info-value {
            font-size: 11px;
            font-weight: bold;
        }
        
        .metrics {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metric-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            margin: 0 5px;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .metric-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .schedule-table th {
            background: #334155;
            color: #ffffff;
            padding: 8px 5px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #475569;
        }
        
        .schedule-table th.time-header {
            font-size: 8px;
            padding: 3px 5px;
        }
        
        .schedule-table td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
            min-height: 60px;
        }
        
        .schedule-table td.day-cell {
            background: #f1f5f9;
            font-weight: bold;
            text-align: center;
            width: 80px;
            font-size: 10px;
        }
        
        .class-block {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 3px solid #3b82f6;
            padding: 6px;
            margin-bottom: 5px;
            border-radius: 4px;
            font-size: 8px;
        }
        
        .class-block .materia {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
            font-size: 9px;
        }
        
        .class-block .info {
            color: #475569;
            line-height: 1.4;
        }
        
        .class-block .info div {
            margin-bottom: 2px;
        }
        
        .empty-slot {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 4px;
            text-align: center;
            padding: 20px 5px;
            color: #cbd5e1;
            font-size: 8px;
            min-height: 60px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            font-size: 8px;
            color: #64748b;
            text-align: center;
        }
        
        .footer .generated {
            margin-bottom: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Horario Generado Automáticamente</h1>
        <div class="subtitle">
            {{ $generacionHorario->gestion->nombre }}
            @if($generacionHorario->carrera)
                - {{ $generacionHorario->carrera->nombre_carrera }}
            @else
                - Toda la Facultad
            @endif
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Generado por</span>
                <span class="info-value">{{ $generacionHorario->usuario->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fecha</span>
                <span class="info-value">{{ $generacionHorario->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Estado</span>
                <span class="info-value">{{ ucfirst($generacionHorario->estado) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Puntuación</span>
                <span class="info-value">{{ $generacionHorario->puntuacion_optimizacion ?? 'N/A' }}/100</span>
            </div>
        </div>
    </div>

    <div class="metrics">
        <div class="metric-card">
            <div class="metric-value">{{ $generacionHorario->total_grupos }}</div>
            <div class="metric-label">Total Grupos</div>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ $generacionHorario->grupos_asignados }}</div>
            <div class="metric-label">Grupos Asignados</div>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ $generacionHorario->porcentaje_exito }}%</div>
            <div class="metric-label">Tasa de Éxito</div>
        </div>
    </div>

    <table class="schedule-table">
        <thead>
            <tr>
                <th rowspan="2">Día</th>
                @foreach($bloques as $bloque)
                    <th>{{ $bloque->etiqueta ?? "Bloque {$bloque->id_bloque}" }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($bloques as $bloque)
                    <th class="time-header">
                        {{ \Carbon\Carbon::parse($bloque->hora_inicio)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($bloque->hora_fin)->format('H:i') }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($dias as $dia => $nombreDia)
                <tr>
                    <td class="day-cell">{{ $nombreDia }}</td>
                    @foreach($bloques as $bloque)
                        <td>
                            @if(isset($matriz[$dia]['bloques'][$bloque->id_bloque]))
                                @foreach($matriz[$dia]['bloques'][$bloque->id_bloque] as $asignacion)
                                    <div class="class-block">
                                        <div class="materia">{{ $asignacion['materia'] }}</div>
                                        <div class="info">
                                            <div><strong>Grupo:</strong> {{ $asignacion['nombre_grupo'] }}</div>
                                            <div><strong>Doc:</strong> {{ $asignacion['docente'] }}</div>
                                            <div><strong>Aula:</strong> {{ $asignacion['aula_codigo'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-slot">Libre</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($generacionHorario->mensaje)
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <div style="font-weight: bold; color: #92400e; margin-bottom: 5px; font-size: 10px;">Observaciones:</div>
            <div style="color: #78350f; font-size: 9px;">{{ $generacionHorario->mensaje }}</div>
        </div>
    @endif

    <div class="footer">
        <div class="generated">
            Documento generado el {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <div>
            Sistema de Gestión de Horarios Académicos - FIC-TIC
        </div>
    </div>
</body>
</html>
