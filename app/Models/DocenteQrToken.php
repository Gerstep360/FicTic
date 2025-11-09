<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class DocenteQrToken extends Model
{
    protected $table = 'docente_qr_tokens';
    protected $primaryKey = 'id_qr_token';
    
    protected $fillable = [
        'id_docente',
        'id_gestion',
        'token',
        'activo',
        'fecha_generacion',
        'fecha_expiracion',
        'veces_usado',
        'ultimo_uso',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
        'fecha_generacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
        'ultimo_uso' => 'datetime',
        'veces_usado' => 'integer',
    ];
    
    // Relaciones
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_docente');
    }
    
    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    public function scopeDeGestion($query, $idGestion)
    {
        return $query->where('id_gestion', $idGestion);
    }
    
    public function scopeVigentes($query)
    {
        return $query->where('activo', true)
                     ->where(function($q) {
                         $q->whereNull('fecha_expiracion')
                           ->orWhere('fecha_expiracion', '>', now());
                     });
    }
    
    // Métodos
    
    /**
     * Genera un token único cifrado para el docente en la gestión
     */
    public static function generarToken($idDocente, $idGestion): string
    {
        $payload = json_encode([
            'docente' => $idDocente,
            'gestion' => $idGestion,
            'timestamp' => now()->timestamp,
            'salt' => Str::random(16),
        ]);
        
        return hash('sha256', $payload);
    }
    
    /**
     * Crea o actualiza el QR del docente para la gestión
     */
    public static function obtenerOCrear($idDocente, $idGestion): self
    {
        $token = static::where('id_docente', $idDocente)
                       ->where('id_gestion', $idGestion)
                       ->first();
        
        if ($token) {
            // Reactivar si estaba inactivo
            if (!$token->activo) {
                $token->update(['activo' => true]);
            }
            return $token;
        }
        
        return static::create([
            'id_docente' => $idDocente,
            'id_gestion' => $idGestion,
            'token' => static::generarToken($idDocente, $idGestion),
            'activo' => true,
            'fecha_generacion' => now(),
        ]);
    }
    
    /**
     * Registra un uso del token
     */
    public function registrarUso(): void
    {
        $this->increment('veces_usado');
        $this->update(['ultimo_uso' => now()]);
    }
    
    /**
     * Desactiva el token
     */
    public function desactivar(): void
    {
        $this->update(['activo' => false]);
    }
    
    /**
     * Genera nuevo token (rotación de seguridad)
     */
    public function regenerar(): void
    {
        $this->update([
            'token' => static::generarToken($this->id_docente, $this->id_gestion),
            'fecha_generacion' => now(),
            'veces_usado' => 0,
            'ultimo_uso' => null,
        ]);
    }
    
    // Atributos computados
    
    public function getEstaVigenteAttribute(): bool
    {
        if (!$this->activo) {
            return false;
        }
        
        if ($this->fecha_expiracion) {
            return $this->fecha_expiracion->isFuture();
        }
        
        return true;
    }
    
    public function getUrlEscaneoAttribute(): string
    {
        return route('asistencia.escanear-qr', ['token' => $this->token]);
    }
    
    public function getDiasVigenciaAttribute(): ?int
    {
        if (!$this->fecha_expiracion) {
            return null;
        }
        
        return now()->diffInDays($this->fecha_expiracion, false);
    }
}
