<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Aula extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'aulas';
    protected $primaryKey = 'id_aula';

    protected $fillable = [
        'codigo', 'tipo', 'capacidad', 'edificio',
    ];

    protected $casts = [
        'capacidad' => 'integer',
    ];

    public function horarios()
    {
        return $this->hasMany(HorarioClase::class, 'id_aula', 'id_aula');
    }
    
    // Para route-model-binding por id_aula
    public function getRouteKeyName(): string
    {
        return 'id_aula';
    }
}
