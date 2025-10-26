<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Trait para registrar eventos en la Bitácora desde cualquier controlador.
 * Delegamos el registro al BitacoraController@store para aprovechar
 * la detección de IP real, User-Agent, device, etc.
 */
trait LogsBitacora
{
    /**
     * Registra en bitácora usando BitacoraController@store.
     *
     * @param Request $request  El request original de la acción actual.
     * @param array   $payload  Campos esperados por BitacoraController@store:
     *   - accion* (string)       - requerido
     *   - tabla_afectada* (string)
     *   - modulo (string)        - opcional
     *   - registro_id (int)      - opcional
     *   - descripcion (string)   - opcional
     *   - id_gestion (int)       - opcional
     *   - exitoso (bool)         - opcional (default=true)
     *   - metadata (array|json)  - opcional (se fusiona con info de cliente)
     *   - cambios_antes (array)  - opcional
     *   - cambios_despues (array)- opcional
     */
    protected function logBitacora(Request $request, array $payload): void
    {
        // Duplicamos el request con el payload de bitácora,
        // así BitacoraController valida/normaliza y captura IP/UA.
        $logRequest = $request->duplicate([], $payload);

        // Llamada interna; ignoramos la respuesta JSON.
        app(\App\Http\Controllers\BitacoraController::class)->store($logRequest);
    }
}
