<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocenteExterno extends Model
{
    use SoftDeletes;

    protected $table = 'docente_externos';
    protected $primaryKey = 'id_docente_externo';

    protected $fillable = [
        'nombre_completo',
        'especialidad',
        'telefono',
        'email',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Suplencias donde este docente externo participó
     */
    public function suplencias()
    {
        return $this->hasMany(Suplencia::class, 'id_docente_externo', 'id_docente_externo');
    }

    /**
     * Scope para obtener solo docentes externos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
