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
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'id_docente', 'id');
    }

    public function horario()
    {
        return $this->belongsTo(HorarioClase::class, 'id_horario', 'id_horario');
    }
}
