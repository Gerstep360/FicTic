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

    public function docenteExterno()
    {
        return $this->belongsTo(DocenteExterno::class, 'id_docente_externo', 'id_docente_externo');
    }

    public function horario()
    {
        return $this->belongsTo(HorarioClase::class, 'id_horario', 'id_horario');
    }

    /**
     * Obtener el nombre del suplente (interno o externo)
     */
    public function getNombreSuplenteAttribute()
    {
        if ($this->id_docente_externo && $this->docenteExterno) {
            return $this->docenteExterno->nombre_completo;
        }
        
        if ($this->docenteSuplente) {
            return $this->docenteSuplente->name;
        }
        
        return 'Sin asignar';
    }

    /**
     * Verificar si es suplente externo
     */
    public function esSuplenteExterno()
    {
        return !is_null($this->id_docente_externo);
    }
}
