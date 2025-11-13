{{-- resources/views/qr-docente/template.blade.php --}}
<div style="background: white; width: 400px; border-radius: 24px; padding: 32px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; font-family: system-ui, -apple-system, sans-serif; margin: 0 auto;">
    
    <!-- Barra superior -->
    <div style="height: 6px; background: linear-gradient(90deg, #6366f1, #8b5cf6, #6366f1); border-radius: 3px; margin: -32px -32px 24px -32px;"></div>

    <!-- Facultad -->
    <p style="font-size: 9px; font-weight: 700; letter-spacing: 2px; color: #9ca3af; text-transform: uppercase; margin: 0 0 8px 0;">
        Facultad de Ciencias y Tecnología
    </p>
    
    <!-- Título -->
    <h1 style="font-size: 20px; font-weight: 900; color: #1e293b; margin: 0 0 24px 0; letter-spacing: -0.5px;">
        Control de Asistencia
    </h1>

    <!-- QR Code -->
    <div style="background: #f8fafc; padding: 20px; border-radius: 20px; margin: 0 0 24px 0; border: 1px solid #e2e8f0;">
        <div style="background: white; padding: 12px; border-radius: 16px; display: inline-block;">
            @php
                $svgBase64 = base64_encode($qrSvg);
                $qrDataUrl = 'data:image/svg+xml;base64,' . $svgBase64;
            @endphp
            <img src="{{ $qrDataUrl }}" alt="QR" style="width: 240px; height: 240px; display: block;">
        </div>
    </div>

    <!-- Nombre Docente -->
    <h2 style="font-size: 18px; font-weight: 900; color: #0f172a; text-transform: uppercase; margin: 0 0 8px 0; letter-spacing: 0.5px; line-height: 1.2;">
        {{ $token->docente->name }}
    </h2>
    
    <!-- Puesto/Rol -->
    <p style="font-size: 11px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 16px 0;">
        {{ $token->docente->roles->pluck('name')->first() ?? 'Docente' }}
    </p>

    <!-- Gestión -->
    <div style="padding: 12px 0; border-top: 1px solid #e5e7eb; margin-top: 16px;">
        <span style="font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 4px;">
            Gestión
        </span>
        <span style="font-size: 12px; font-weight: 700; color: #6366f1;">
            {{ $token->gestion->nombre }}
        </span>
    </div>

</div>