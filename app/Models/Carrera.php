<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'carreras';
    protected $primaryKey = 'id_carrera';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'id_facultad', 'id_facultad');
    }

    public function materias()
    {
        return $this->hasMany(\App\Models\Materia::class, 'id_carrera', 'id_carrera');
    }
}
