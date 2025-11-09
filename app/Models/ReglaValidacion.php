<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReglaValidacion extends Model
{
    use HasFactory;

    protected $table = 'reglas_validacion';
    protected $primaryKey = 'id_regla';

    protected $fillable = [
        'id_facultad',
        'id_carrera',
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'severidad',
        'activa',
        'bloqueante',
        'parametros',
    ];

    protected $casts = [
        'parametros' => 'array',
        'activa' => 'boolean',
        'bloqueante' => 'boolean',
    ];

    // Relaciones
    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'id_facultad');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeBloqueantes($query)
    {
        return $query->where('bloqueante', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorSeveridad($query, $severidad)
    {
        return $query->where('severidad', $severidad);
    }

    public function scopeGlobales($query)
    {
        return $query->whereNull('id_facultad')->whereNull('id_carrera');
    }

    public function scopeDeFacultad($query, $idFacultad)
    {
        return $query->where('id_facultad', $idFacultad)->whereNull('id_carrera');
    }

    public function scopeDeCarrera($query, $idCarrera)
    {
        return $query->where('id_carrera', $idCarrera);
    }

    // Métodos auxiliares
    public function getAlcanceTextoAttribute()
    {
        if ($this->id_carrera) {
            return "Carrera: " . ($this->carrera->nombre_carrera ?? 'N/A');
        }
        if ($this->id_facultad) {
            return "Facultad: " . ($this->facultad->nombre ?? 'N/A');
        }
        return "Global";
    }

    public function getColorSeveridadAttribute()
    {
        return match($this->severidad) {
            'critica' => 'red',
            'alta' => 'orange',
            'media' => 'yellow',
            'baja' => 'blue',
            default => 'gray',
        };
    }

    public function getIconoSeveridadAttribute()
    {
        return match($this->severidad) {
            'critica' => '🔴',
            'alta' => '🟠',
            'media' => '🟡',
            'baja' => '🔵',
            default => '⚪',
        };
    }
}
