<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reprogramacion extends Model
{
    use HasFactory;

    protected $table = 'reprogramaciones';
    protected $primaryKey = 'id_reprogramacion';

    protected $fillable = [
        'id_horario_original',
        'fecha_original',
        'id_aula_nueva',
        'fecha_nueva',
        'tipo',
        'motivo',
        'estado',
        'solicitado_por',
        'aprobado_por',
        'observaciones',
        'fecha_solicitud',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_original' => 'date',
        'fecha_nueva' => 'date',
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    /**
     * Relación con el horario original
     */
    public function horarioOriginal(): BelongsTo
    {
        return $this->belongsTo(HorarioClase::class, 'id_horario_original', 'id_horario');
    }

    /**
     * Relación con el aula nueva (si aplica)
     */
    public function aulaNueva(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'id_aula_nueva', 'id_aula');
    }

    /**
     * Usuario que solicitó la reprogramación
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por', 'id');
    }

    /**
     * Usuario que aprobó/rechazó la reprogramación
     */
    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por', 'id');
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Verificar si está pendiente
     */
    public function isPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    /**
     * Verificar si está aprobada
     */
    public function isAprobada(): bool
    {
        return $this->estado === 'APROBADA';
    }
}
