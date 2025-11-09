<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;

    protected $table = 'gestiones';
    protected $primaryKey = 'id_gestion';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'publicada' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_publicacion' => 'datetime',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_gestion', 'id_gestion');
    }
    
    public function feriados() {
        return $this->hasMany(Feriado::class, 'id_gestion', 'id_gestion');
    }
    
    public function usuarioPublicador()
    {
        return $this->belongsTo(User::class, 'publicada_por');
    }
    
    public function aprobaciones()
    {
        return $this->hasMany(AprobacionHorario::class, 'id_gestion', 'id_gestion');
    }

    // Scopes
    public function scopePublicadas($query)
    {
        return $query->where('publicada', true);
    }

    public function scopeNoPublicadas($query)
    {
        return $query->where('publicada', false);
    }

    // Métodos auxiliares
    public function publicar($idUsuario, $nota = null)
    {
        $this->update([
            'publicada' => true,
            'fecha_publicacion' => now(),
            'publicada_por' => $idUsuario,
            'nota_publicacion' => $nota,
        ]);
    }

    public function despublicar()
    {
        $this->update([
            'publicada' => false,
            'fecha_publicacion' => null,
            'publicada_por' => null,
            'nota_publicacion' => null,
        ]);
    }

    public function getPuedePublicarAttribute(): bool
    {
        // Admin DTIC puede publicar siempre (bypass)
        if (auth()->check() && auth()->user()->hasRole('Admin DTIC')) {
            return true;
        }

        // Verificar que todas las aprobaciones de carrera estén en estado aprobado_final
        $totalAprobaciones = $this->aprobaciones()->count();
        
        if ($totalAprobaciones === 0) {
            return false;
        }

        $aprobacionesPendientes = $this->aprobaciones()
            ->whereNotIn('estado', ['aprobado_final'])
            ->count();

        return $aprobacionesPendientes === 0;
    }

    public function getEstadoPublicacionAttribute(): string
    {
        if ($this->publicada) {
            return 'Publicada';
        }

        if ($this->puede_publicar) {
            return 'Lista para Publicar';
        }

        return 'En Proceso';
    }
}
