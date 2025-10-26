<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\Facultad;

class CheckAmbito
{
    /**
     * Usar como: ->middleware('ambito:carrera') ó ->middleware('ambito:facultad')
     * Opcional 2º argumento para nombre del parámetro de ruta: ambito:carrera,id_carrera
     */
    public function handle(Request $request, Closure $next, string $tipoParam, ?string $paramName = null)
    {
        $user = $request->user();
        if (!$user) abort(401);

        // Acceso total
        if (method_exists($user, 'hasRole') && $user->hasRole('Admin DTIC')) {
            return $next($request);
        }

        // Resolver id desde la ruta (acepta model binding o id plano)
        $paramName = $paramName ?: $tipoParam; // por defecto {carrera} o {facultad}
        $routeVal  = $request->route($paramName);

        $resolverId = function ($val, string $model) {
            if ($val instanceof $model) return (int)$val->getKey();
            if (is_scalar($val) && ctype_digit((string)$val)) return (int)$val;
            return null;
        };

        if ($tipoParam === 'carrera') {
            $carreraId = $resolverId($routeVal, Carrera::class);
            if (!$carreraId) abort(400, 'Parámetro de carrera inválido.');
            if (!in_array($carreraId, $user->allowedCarreraIds(), true)) abort(403);
        } elseif ($tipoParam === 'facultad') {
            $facultadId = $resolverId($routeVal, Facultad::class);
            if (!$facultadId) abort(400, 'Parámetro de facultad inválido.');
            if (!in_array($facultadId, $user->allowedFacultadIds(), true)) abort(403);
        } else {
            abort(500, 'Configuración de middleware ambito inválida.');
        }

        return $next($request);
    }
}
