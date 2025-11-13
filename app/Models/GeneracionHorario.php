<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneracionHorario extends Model
{
    use HasFactory;

    protected $table = 'generacion_horarios';
    protected $primaryKey = 'id_generacion';

    protected $fillable = [
        'id_gestion',
        'id_carrera',
        'id_usuario',
        'configuracion',
        'estado',
        'is_seleccionado',
        'resultado',
        'mensaje',
        'total_grupos',
        'grupos_asignados',
        'conflictos_detectados',
        'puntuacion_optimizacion',
        'fecha_inicio',
        'fecha_fin',
        'duracion_segundos',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'resultado' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'puntuacion_optimizacion' => 'decimal:2',
    ];

    // Relaciones
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Scopes
    public function scopeEnGestion($query, $idGestion)
    {
        return $query->where('id_gestion', $idGestion);
    }

    public function scopeDeCarrera($query, $idCarrera)
    {
        return $query->where('id_carrera', $idCarrera);
    }

    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Métodos auxiliares
    public function marcarComoProcesando()
    {
        $this->update([
            'estado' => 'procesando',
            'fecha_inicio' => now(),
        ]);
    }

    public function marcarComoCompletado($resultado, $metricas = [])
    {
        $this->update([
            'estado' => 'completado',
            'resultado' => $resultado,
            'fecha_fin' => now(),
            'duracion_segundos' => $this->fecha_inicio ? now()->diffInSeconds($this->fecha_inicio) : null,
            'total_grupos' => $metricas['total_grupos'] ?? 0,
            'grupos_asignados' => $metricas['grupos_asignados'] ?? 0,
            'conflictos_detectados' => $metricas['conflictos_detectados'] ?? 0,
            'puntuacion_optimizacion' => $metricas['puntuacion_optimizacion'] ?? null,
        ]);
    }

    public function marcarComoError($mensaje)
    {
        $this->update([
            'estado' => 'error',
            'mensaje' => $mensaje,
            'fecha_fin' => now(),
            'duracion_segundos' => $this->fecha_inicio ? now()->diffInSeconds($this->fecha_inicio) : null,
        ]);
    }

    public function marcarComoAplicado()
    {
        $this->update(['estado' => 'aplicado']);
    }

    // Atributos computados
    public function getEsCompletadoAttribute()
    {
        return $this->estado === 'completado';
    }

    public function getEsAplicadoAttribute()
    {
        return $this->estado === 'aplicado';
    }

    public function getPuedeAplicarseAttribute()
    {
        // Puede aplicarse si está completado O si ya fue aplicado (para poder revertir)
        return in_array($this->estado, ['completado', 'aplicado']) && !empty($this->resultado);
    }

    public function seleccionar()
    {
        // Deseleccionar todas las demás generaciones de la misma gestión
        static::where('id_gestion', $this->id_gestion)
            ->where('id_generacion', '!=', $this->id_generacion)
            ->update(['is_seleccionado' => false]);
        
        // Seleccionar esta
        $this->update(['is_seleccionado' => true]);
    }

    public function getPorcentajeExitoAttribute()
    {
        if ($this->total_grupos == 0) return 0;
        return round(($this->grupos_asignados / $this->total_grupos) * 100, 2);
    }

    public function getAlcanceTextoAttribute()
    {
        if ($this->id_carrera) {
            return $this->carrera ? $this->carrera->nombre_carrera : 'Carrera';
        }
        return 'Toda la Facultad';
    }
}
