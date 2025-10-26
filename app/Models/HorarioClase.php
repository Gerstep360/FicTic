<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioClase extends Model
{
    use HasFactory;

    protected $table = 'horario_clases';
    protected $primaryKey = 'id_horario';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function bloque()
    {
        return $this->belongsTo(Bloque::class, 'id_bloque', 'id_bloque');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'id_docente', 'id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_horario', 'id_horario');
    }

    public function suplencias()
    {
        return $this->hasMany(Suplencia::class, 'id_horario', 'id_horario');
    }
}
