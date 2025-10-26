<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacoras';
    protected $primaryKey = 'id_bitacora';
    public $incrementing = true;
    // BIGINT en BD, pero para Eloquent el $keyType puede quedarse como 'int'
    protected $keyType = 'int';

    protected $fillable = [
        'id_usuario',
        'accion',
        'modulo',
        'tabla_afectada',
        'registro_id',
        'descripcion',
        'id_gestion',
        'ip',
        'user_agent',
        'url',
        'metodo',
        'exitoso',
        'metadata',
        'cambios_antes',
        'cambios_despues',
    ];

    protected $casts = [
        'exitoso'        => 'boolean',
        'metadata'       => 'array',
        'cambios_antes'  => 'array',
        'cambios_despues'=> 'array',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    protected $attributes = [
        'exitoso' => true,
    ];

    /* =========================
     * Relaciones
     * ========================= */

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    // Nota: asume que tienes un modelo Gestion que mapea a la tabla 'gestiones'
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    /* =========================
     * Scopes de consulta
     * ========================= */

    public function scopeAccion(Builder $q, string $accion): Builder
    {
        return $q->where('accion', $accion);
    }

    public function scopeModulo(Builder $q, ?string $modulo): Builder
    {
        return $modulo ? $q->where('modulo', $modulo) : $q;
    }

    public function scopeObjeto(Builder $q, string $tabla, $id): Builder
    {
        return $q->where('tabla_afectada', $tabla)
                 ->where('registro_id', $id);
    }

    public function scopeEnGestion(Builder $q, $idGestion): Builder
    {
        return $q->where('id_gestion', $idGestion);
    }

    public function scopeRangoFechas(Builder $q, $desde, $hasta): Builder
    {
        return $q->whereBetween('created_at', [$desde, $hasta]);
    }

    /* =========================
     * Helper para registrar
     * ========================= */

    /**
     * Crea un registro de bitácora de forma sencilla.
     *
     * @param  array  $data  [
     *   'accion' (req), 'tabla_afectada' (req), 'registro_id' (opt),
     *   'descripcion' (opt), 'modulo' (opt), 'id_gestion' (opt),
     *   'exitoso' (opt, bool), 'metadata' (opt, array),
     *   'cambios_antes' (opt, array), 'cambios_despues' (opt, array),
     *   'ip' (opt), 'user_agent' (opt), 'url' (opt), 'metodo' (opt)
     * ]
     */
    public static function log(array $data): self
    {
        $defaults = [
            'id_usuario' => Auth::id(),
            'ip'         => request()->ip() ?? null,
            'user_agent' => request()->header('User-Agent') ?? null,
            'url'        => request()->fullUrl() ?? null,
            'metodo'     => request()->method() ?? null,
            'exitoso'    => true,
        ];

        return static::create(array_merge($defaults, $data));
    }
}
