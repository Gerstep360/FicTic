<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes;

    protected $table = 'grupos';
    protected $primaryKey = 'id_grupo';

    protected $fillable = [
        'nombre_grupo',
        'turno',
        'modalidad',
        'cupo',
        'id_materia',
        'id_gestion',
        'id_docente',
    ];
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'id_docente', 'id');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioClase::class, 'id_grupo', 'id_grupo');
    }
}
