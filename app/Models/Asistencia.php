<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';
    protected $primaryKey = 'id_asistencia';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'es_manual' => 'boolean',
    ];

    // Scopes
    public function scopeManuales($query)
    {
        return $query->where('es_manual', true);
    }

    public function scopeAutomaticas($query)
    {
        return $query->where('es_manual', false);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', strtoupper($estado));
    }

    public function scopeEnRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }

    // Relaciones
    public function docente()
    {
        return $this->belongsTo(User::class, 'id_docente', 'id');
    }

    public function horario()
    {
        return $this->belongsTo(HorarioClase::class, 'id_horario', 'id_horario');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por', 'id');
    }

    // Alias para registrador
    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por', 'id');
    }
}
