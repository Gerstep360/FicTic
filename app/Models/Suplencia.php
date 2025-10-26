<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplencia extends Model
{
    use HasFactory;

    protected $table = 'suplencias';
    protected $primaryKey = 'id_suplencia';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'fecha_clase' => 'date',
    ];

    public function docenteAusente()
    {
        return $this->belongsTo(User::class, 'id_docente_ausente', 'id');
    }

    public function docenteSuplente()
    {
        return $this->belongsTo(User::class, 'id_docente_suplente', 'id');
    }

    public function horario()
    {
        return $this->belongsTo(HorarioClase::class, 'id_horario', 'id_horario');
    }
}
