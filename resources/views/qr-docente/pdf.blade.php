<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>QR {{ $token->docente->name }} - {{ $token->gestion->nombre }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            min-height: 100vh;
        }
        
        .page-container {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        /* Header con gradiente */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 40px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 13px;
            opacity: 0.95;
            font-weight: 500;
            letter-spacing: 1px;
        }
        
        /* Contenido principal */
        .content {
            padding: 40px;
        }
        
        /* QR Card - Igual que en el template */
        .qr-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        /* Info Cards */
        .info-card {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
        }
        
        .info-card h3 {
            font-size: 14px;
            color: #667eea;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .info-card h3::before {
            content: '';
            width: 4px;
            height: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin-right: 10px;
            border-radius: 2px;
        }
        
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .info-label {
            width: 45%;
            font-size: 13px;
            color: #6c757d;
            font-weight: 600;
        }
        
        .info-value {
            width: 55%;
            font-size: 13px;
            color: #212529;
            font-weight: 500;
        }
        
        /* Badge de estado */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Instructions Box */
        .instructions {
            background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%);
            border-radius: 16px;
            padding: 25px;
            margin-top: 20px;
            border: 2px solid #c7d2fe;
        }
        
        .instructions h4 {
            font-size: 14px;
            color: #4c1d95;
            font-weight: 800;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .instructions ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .instructions li {
            font-size: 12px;
            color: #5b21b6;
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        /* Warning Box */
        .warning-box {
            background: #fff3cd;
            border: 3px solid #ffc107;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }
        
        .warning-box p {
            font-size: 11px;
            color: #856404;
            font-weight: 700;
            line-height: 1.6;
        }
        
        .warning-box .icon {
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        /* Footer */
        .footer {
            background: #f8f9fa;
            padding: 25px 40px;
            text-align: center;
            border-top: 3px solid #e9ecef;
        }
        
        .footer p {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .footer .stamp {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        /* Decoración */
        .decoration-bar {
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
        }
    </style>
</head>
<body>
    <div class="page-container">
        
        <!-- Barra decorativa superior -->
        <div class="decoration-bar"></div>
        
        <!-- Header -->
        <div class="header">
            <h1>CÓDIGO QR OFICIAL</h1>
            <p>Control de Asistencia Docente • Sistema FicTic</p>
        </div>
        
        <!-- Contenido -->
        <div class="content">
            
            <!-- QR Code -->
            <div class="qr-wrapper">
                {!! $qrHtml !!}
            </div>
            
            <!-- Información del Docente -->
            <div class="info-card">
                <h3>Información del Docente</h3>
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $token->docente->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $token->docente->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Roles:</span>
                    <span class="info-value">{{ $token->docente->roles->pluck('name')->join(', ') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        @if($token->activo)
                            <span class="status-badge status-active">✓ Activo</span>
                        @else
                            <span class="status-badge status-inactive">✗ Inactivo</span>
                        @endif
                    </span>
                </div>
            </div>
            
            <!-- Gestión -->
            <div class="info-card">
                <h3>Gestión Académica</h3>
                <div class="info-row">
                    <span class="info-label">Gestión:</span>
                    <span class="info-value">{{ $token->gestion->nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Generado:</span>
                    <span class="info-value">{{ $token->fecha_generacion->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Escaneos:</span>
                    <span class="info-value">{{ $token->veces_usado }} veces</span>
                </div>
            </div>
            
            <!-- Instrucciones -->
            <div class="instructions">
                <h4>📱 Instrucciones de Uso</h4>
                <ul>
                    <li>Presente este código QR al sistema de control de asistencia</li>
                    <li>Mantenga el código limpio y sin arrugas si está impreso</li>
                    <li>Si usa dispositivo móvil, aumente el brillo de pantalla</li>
                    <li>El sistema registrará automáticamente su asistencia</li>
                    <li>Conserve este documento durante toda la gestión académica</li>
                </ul>
            </div>
            
            <!-- Advertencia -->
            <div class="warning-box">
                <div class="icon">⚠️</div>
                <p>
                    CÓDIGO PERSONAL E INTRANSFERIBLE<br>
                    No comparta este código. El uso indebido está sujeto a sanciones.<br>
                    Cualquier irregularidad será reportada a las autoridades competentes.
                </p>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Documento generado: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Facultad de Ciencias y Tecnología</p>
            <div class="stamp">✓ DOCUMENTO OFICIAL</div>
        </div>
        
        <!-- Barra decorativa inferior -->
        <div class="decoration-bar"></div>
        
    </div>
</body>
</html>
