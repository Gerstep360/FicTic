{{-- resources/views/qr-docente/template.blade.php --}}
<div id="qr-card-export" style="background: white; width: 100%; max-width: 400px; border-radius: 16px; padding: 24px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; font-family: 'Courier New', monospace; margin: 0 auto;">
    
    <!-- Facultad -->
    <p style="font-size: 11px; font-weight: 600; letter-spacing: 1px; color: #1e293b; text-transform: uppercase; margin: 0 0 4px 0; line-height: 1.4;">
        FACULTAD DE CIENCIAS Y TECNOLOGÍA
    </p>
    
    <!-- Título -->
    <h1 style="font-size: 12px; font-weight: 600; color: #64748b; margin: 0 0 20px 0; letter-spacing: 0.5px;">
        CONTROL DE ASISTENCIA
    </h1>

    <!-- QR Code -->
    <div style="padding: 8px; margin: 0 0 20px 0;">
        @php
            $svgBase64 = base64_encode($qrSvg);
            $qrDataUrl = 'data:image/svg+xml;base64,' . $svgBase64;
        @endphp
        <img src="{{ $qrDataUrl }}" alt="QR" style="width: 100%; max-width: 280px; height: auto; display: block; margin: 0 auto;">
    </div>

    <!-- Nombre Docente -->
    <h2 style="font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin: 0 0 4px 0; letter-spacing: 0.3px; line-height: 1.3;">
        {{ $token->docente->name }}
    </h2>
    
    <!-- Puesto/Rol -->
    <p style="font-size: 11px; font-weight: 600; color: #64748b; margin: 0 0 16px 0;">
        {{ $token->docente->roles->pluck('name')->first() ?? 'Admin DTIC' }}
    </p>

    <!-- Gestión -->
    <div style="padding: 8px 0; border-top: 1px solid #e5e7eb; margin-top: 12px;">
        <span style="font-size: 10px; font-weight: 600; color: #64748b; display: block; margin-bottom: 2px;">
            Gestión {{ $token->gestion->nombre }}
        </span>
    </div>

    <!-- Línea separadora inferior -->
    <div style="width: 60px; height: 2px; background: #1e293b; margin: 12px auto 0; border-radius: 1px;"></div>

</div>