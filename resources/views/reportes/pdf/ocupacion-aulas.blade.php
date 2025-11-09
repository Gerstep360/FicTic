<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ocupación de Aulas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #7c3aed; margin-bottom: 10px; }
        h2 { text-align: center; color: #475569; font-size: 14px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #7c3aed; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .progress-bar { background-color: #e0e7ff; height: 20px; border-radius: 10px; overflow: hidden; }
        .progress-fill { background-color: #7c3aed; height: 100%; text-align: center; color: white; font-size: 10px; line-height: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Reporte de Ocupación de Aulas</h1>
    <h2>Porcentaje de Uso de Espacios</h2>
    <p style="text-align: center;"><strong>Total de Slots Disponibles por Aula:</strong> {{ $totalSlots }} ({{ \App\Models\Bloque::count() }} bloques × 6 días)</p>
    <p style="text-align: center;"><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Aula</th>
                <th>Tipo</th>
                <th>Capacidad</th>
                <th>Edificio</th>
                <th>Slots Ocupados</th>
                <th>Ocupación (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ocupacion as $aula)
                <tr>
                    <td><strong>{{ $aula['codigo'] }}</strong></td>
                    <td>{{ $aula['tipo'] }}</td>
                    <td>{{ $aula['capacidad'] ?? 'N/A' }}</td>
                    <td>{{ $aula['edificio'] ?? 'N/A' }}</td>
                    <td>{{ $aula['slots_ocupados'] }} / {{ $aula['total_slots'] }}</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $aula['porcentaje'] }}%;">
                                {{ $aula['porcentaje'] }}%
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado por Sistema de Gestión Académica - FicTic</p>
        <p>Este reporte ayuda a identificar aulas infrautilizadas y optimizar el uso de espacios.</p>
    </div>
</body>
</html>
