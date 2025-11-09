<?php

namespace App\Support;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Trait para registrar eventos en la Bitácora desde cualquier controlador.
 */
trait LogsBitacora
{
    /**
     * Registra en bitácora directamente en la base de datos.
     *
     * @param Request $request  El request original de la acción actual.
     * @param array   $payload  Campos:
     *   - accion* (string)       - requerido
     *   - tabla_afectada* (string) - requerido
     *   - modulo (string)        - opcional
     *   - registro_id (int)      - opcional
     *   - descripcion (string)   - opcional
     *   - id_gestion (int)       - opcional
     *   - exitoso (bool)         - opcional (default=true)
     *   - metadata (array)       - opcional
     *   - cambios_antes (array)  - opcional
     *   - cambios_despues (array)- opcional
     */
    protected function logBitacora(Request $request, array $payload): void
    {
        try {
            // Obtener información del cliente
            $userAgent = $request->header('User-Agent', 'Unknown');
            $ip = $request->ip();
            
            // Preparar metadata
            $metadata = $payload['metadata'] ?? [];
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true) ?? [];
            }
            
            // Agregar información del cliente al metadata
            $metadata['ip'] = $ip;
            $metadata['user_agent'] = $userAgent;
            $metadata['url'] = $request->fullUrl();
            $metadata['method'] = $request->method();
            
            // Crear registro en bitácora
            Bitacora::create([
                'id_usuario' => Auth::id(),
                'accion' => $payload['accion'],
                'modulo' => $payload['modulo'] ?? null,
                'tabla_afectada' => $payload['tabla_afectada'],
                'registro_id' => $payload['registro_id'] ?? null,
                'descripcion' => $payload['descripcion'] ?? null,
                'id_gestion' => $payload['id_gestion'] ?? null,
                'exitoso' => $payload['exitoso'] ?? true,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'metadata' => $metadata,
                'cambios_antes' => $payload['cambios_antes'] ?? null,
                'cambios_despues' => $payload['cambios_despues'] ?? null,
            ]);
        } catch (\Exception $e) {
            // Si falla el registro en bitácora, no interrumpir el flujo principal
            \Log::error('Error al registrar en bitácora: ' . $e->getMessage());
        }
    }
}
