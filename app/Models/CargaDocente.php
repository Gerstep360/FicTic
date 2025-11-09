<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargaDocente extends Model
{
    protected $table = 'cargas_docentes';
    protected $primaryKey = 'id_carga';

    protected $fillable = [
        'id_docente',
        'id_gestion',
        'id_carrera',
        'horas_contratadas',
        'horas_asignadas',
        'tipo_contrato',
        'categoria',
        'restricciones_horario',
        'observaciones',
    ];

    protected $casts = [
        'horas_contratadas' => 'integer',
        'horas_asignadas' => 'integer',
        'restricciones_horario' => 'array',
    ];

    /**
     * Relación con el docente (User)
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_docente');
    }

    /**
     * Relación con la gestión
     */
    public function gestion(): BelongsTo
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    /**
     * Relación con la carrera (puede ser null si es general)
     */
    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    /**
     * Scope: filtrar por gestión
     */
    public function scopeEnGestion($query, int $idGestion)
    {
        return $query->where('id_gestion', $idGestion);
    }

    /**
     * Scope: filtrar por carrera
     */
    public function scopeDeCarrera($query, int $idCarrera)
    {
        return $query->where('id_carrera', $idCarrera);
    }

    /**
     * Calcula las horas disponibles
     */
    public function getHorasDisponiblesAttribute(): int
    {
        return max(0, $this->horas_contratadas - $this->horas_asignadas);
    }

    /**
     * Verifica si se excedió la carga
     */
    public function getExcedidoAttribute(): bool
    {
        return $this->horas_asignadas > $this->horas_contratadas;
    }

    /**
     * Porcentaje de ocupación
     */
    public function getPorcentajeOcupacionAttribute(): float
    {
        if ($this->horas_contratadas == 0) return 0;
        return round(($this->horas_asignadas / $this->horas_contratadas) * 100, 2);
    }
}
