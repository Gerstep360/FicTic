<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AprobacionHorario extends Model
{
    protected $table = 'aprobaciones_horario';
    protected $primaryKey = 'id_aprobacion';

    protected $fillable = [
        'id_gestion',
        'id_carrera',
        'estado',
        'total_horarios',
        'horarios_validados',
        'conflictos_pendientes',
        'id_coordinador',
        'id_director',
        'id_decano',
        'fecha_envio_director',
        'fecha_respuesta_director',
        'fecha_envio_decano',
        'fecha_respuesta_decano',
        'fecha_publicacion',
        'observaciones_director',
        'observaciones_decano',
        'observaciones_coordinador',
        'metadata',
    ];

    protected $casts = [
        'fecha_envio_director' => 'datetime',
        'fecha_respuesta_director' => 'datetime',
        'fecha_envio_decano' => 'datetime',
        'fecha_respuesta_decano' => 'datetime',
        'fecha_publicacion' => 'datetime',
        'metadata' => 'array',
        'total_horarios' => 'integer',
        'horarios_validados' => 'integer',
        'conflictos_pendientes' => 'integer',
    ];

    // ==================== Relaciones ====================

    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    public function coordinador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_coordinador');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_director');
    }

    public function decano(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_decano');
    }

    // ==================== Scopes ====================

    public function scopeDeCarrera($query, $idCarrera)
    {
        return $query->where('id_carrera', $idCarrera);
    }

    public function scopeDeGestion($query, $idGestion)
    {
        return $query->where('id_gestion', $idGestion);
    }

    public function scopeFacultativas($query)
    {
        return $query->whereNull('id_carrera');
    }

    public function scopePendientesDirector($query)
    {
        return $query->where('estado', 'pendiente_director');
    }

    public function scopePendientesDecano($query)
    {
        return $query->where('estado', 'pendiente_decano');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobado_final');
    }

    // ==================== Métodos de Estado ====================

    /**
     * Enviar para aprobación del Director
     */
    public function enviarADirector(int $idCoordinador): void
    {
        $this->update([
            'estado' => 'pendiente_director',
            'id_coordinador' => $idCoordinador,
            'fecha_envio_director' => now(),
        ]);
    }

    /**
     * Director aprueba el horario
     */
    public function aprobarDirector(int $idDirector, ?string $observaciones = null): void
    {
        $this->update([
            'estado' => 'aprobado_director',
            'id_director' => $idDirector,
            'fecha_respuesta_director' => now(),
            'observaciones_director' => $observaciones,
        ]);
    }

    /**
     * Director observa y solicita cambios
     */
    public function observarDirector(int $idDirector, string $observaciones): void
    {
        $this->update([
            'estado' => 'observado_director',
            'id_director' => $idDirector,
            'fecha_respuesta_director' => now(),
            'observaciones_director' => $observaciones,
        ]);
    }

    /**
     * Enviar al Decano (consolidado facultativo)
     */
    public function enviarADecano(): void
    {
        $this->update([
            'estado' => 'pendiente_decano',
            'fecha_envio_decano' => now(),
        ]);
    }

    /**
     * Decano aprueba (aprobación final)
     */
    public function aprobarDecano(int $idDecano, ?string $observaciones = null): void
    {
        $this->update([
            'estado' => 'aprobado_final',
            'id_decano' => $idDecano,
            'fecha_respuesta_decano' => now(),
            'observaciones_decano' => $observaciones,
        ]);
    }

    /**
     * Decano observa y solicita cambios
     */
    public function observarDecano(int $idDecano, string $observaciones): void
    {
        $this->update([
            'estado' => 'observado_decano',
            'id_decano' => $idDecano,
            'fecha_respuesta_decano' => now(),
            'observaciones_decano' => $observaciones,
        ]);
    }

    /**
     * Coordinador responde a observaciones
     */
    public function responderObservaciones(string $respuesta): void
    {
        $this->update([
            'estado' => 'borrador',
            'observaciones_coordinador' => $respuesta,
        ]);
    }

    /**
     * Rechazar definitivamente
     */
    public function rechazar(int $idUsuario, string $motivo): void
    {
        $metadataActual = $this->metadata ?? [];
        $metadataActual['rechazo'] = [
            'usuario_id' => $idUsuario,
            'motivo' => $motivo,
            'fecha' => now()->toIso8601String(),
        ];

        $this->update([
            'estado' => 'rechazado',
            'metadata' => $metadataActual,
        ]);
    }

    /**
     * Marcar como publicado
     */
    public function marcarPublicado(): void
    {
        $this->update([
            'fecha_publicacion' => now(),
        ]);
    }

    // ==================== Atributos Computados ====================

    /**
     * Verificar si puede ser enviado al Director
     */
    public function getPuedeEnviarDirectorAttribute(): bool
    {
        return in_array($this->estado, ['borrador', 'observado_director']);
    }

    /**
     * Verificar si puede ser aprobado por Director
     */
    public function getPuedeAprobarDirectorAttribute(): bool
    {
        return $this->estado === 'pendiente_director';
    }

    /**
     * Verificar si puede ser enviado al Decano
     */
    public function getPuedeEnviarDecanoAttribute(): bool
    {
        return $this->estado === 'aprobado_director';
    }

    /**
     * Verificar si puede ser aprobado por Decano
     */
    public function getPuedeAprobarDecanoAttribute(): bool
    {
        return $this->estado === 'pendiente_decano';
    }

    /**
     * Verificar si puede ser publicado
     */
    public function getPuedePublicarAttribute(): bool
    {
        return $this->estado === 'aprobado_final' && $this->fecha_publicacion === null;
    }

    /**
     * Alcance textual
     */
    public function getAlcanceTextoAttribute(): string
    {
        if ($this->id_carrera) {
            return $this->carrera ? $this->carrera->nombre_carrera : "Carrera #{$this->id_carrera}";
        }
        return 'Toda la Facultad';
    }

    /**
     * Estado con formato amigable
     */
    public function getEstadoTextoAttribute(): string
    {
        return match($this->estado) {
            'borrador' => 'En Elaboración',
            'pendiente_director' => 'Pendiente de Director',
            'observado_director' => 'Observado por Director',
            'aprobado_director' => 'Aprobado por Director',
            'pendiente_decano' => 'Pendiente de Decano',
            'observado_decano' => 'Observado por Decano',
            'aprobado_final' => 'Aprobado (Listo para Publicar)',
            'rechazado' => 'Rechazado',
            default => 'Desconocido',
        };
    }

    /**
     * Color del estado para UI
     */
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'borrador' => 'bg-slate-700 text-slate-300',
            'pendiente_director', 'pendiente_decano' => 'bg-yellow-900/50 border-yellow-700 text-yellow-300',
            'observado_director', 'observado_decano' => 'bg-orange-900/50 border-orange-700 text-orange-300',
            'aprobado_director' => 'bg-blue-900/50 border-blue-700 text-blue-300',
            'aprobado_final' => 'bg-green-900/50 border-green-700 text-green-300',
            'rechazado' => 'bg-red-900/50 border-red-700 text-red-300',
            default => 'bg-slate-700 text-slate-300',
        };
    }

    /**
     * Icono del estado
     */
    public function getIconoEstadoAttribute(): string
    {
        return match($this->estado) {
            'borrador' => '📝',
            'pendiente_director', 'pendiente_decano' => '⏳',
            'observado_director', 'observado_decano' => '⚠️',
            'aprobado_director' => '✓',
            'aprobado_final' => '✅',
            'rechazado' => '❌',
            default => '❓',
        };
    }

    /**
     * Porcentaje de progreso
     */
    public function getPorcentajeProgresoAttribute(): int
    {
        if ($this->total_horarios === 0) {
            return 0;
        }
        return (int) round(($this->horarios_validados / $this->total_horarios) * 100);
    }

    /**
     * Tiempo en estado actual
     */
    public function getTiempoEnEstadoAttribute(): string
    {
        $fechaReferencia = match($this->estado) {
            'pendiente_director' => $this->fecha_envio_director,
            'observado_director', 'aprobado_director' => $this->fecha_respuesta_director,
            'pendiente_decano' => $this->fecha_envio_decano,
            'observado_decano', 'aprobado_final' => $this->fecha_respuesta_decano,
            default => $this->updated_at,
        };

        if (!$fechaReferencia) {
            return 'Fecha no disponible';
        }

        return $fechaReferencia->diffForHumans();
    }
}
