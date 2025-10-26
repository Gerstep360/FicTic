<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materia extends Model
{
    use SoftDeletes;

    protected $table = 'materias';
    protected $primaryKey = 'id_materia';

    protected $fillable = [
        'codigo', 'nombre', 'nivel', 'creditos', 'id_carrera',
    ];

    // Para route-model-binding por id_materia (en rutas anidadas)
    public function getRouteKeyName(): string
    {
        return 'id_materia';
    }

    public function carrera()
    {
        return $this->belongsTo(\App\Models\Carrera::class, 'id_carrera', 'id_carrera');
    }

    // Prerrequisitos de esta materia
    public function prerrequisitos()
    {
        return $this->belongsToMany(
            \App\Models\Materia::class,
            'materia_prerrequisitos',
            'id_materia',
            'id_requisito'
        );
    }

    // Materias que dependen de esta como requisito
    public function esPrerequisitoDe()
    {
        return $this->belongsToMany(
            \App\Models\Materia::class,
            'materia_prerrequisitos',
            'id_requisito',
            'id_materia'
        );
    }
        public function grupos()
    {
        return $this->hasMany(\App\Models\Grupo::class, 'id_materia', 'id_materia');
    }
}
