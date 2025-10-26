<p>Hola {{ $nombre }},</p>
<p>Se ha generado tu cuenta en el sistema de FICCT.</p>
<p><strong>Usuario:</strong> {{ $email }}<br>
<strong>Contraseña temporal:</strong> {{ $passwordPlano }}</p>
<p>Accede aquí: <a href="{{ $urlLogin }}">{{ $urlLogin }}</a></p>
<p>Por seguridad, cambia tu contraseña en tu primer ingreso.</p>
