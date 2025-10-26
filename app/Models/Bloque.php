<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloque extends Model
{
    use HasFactory;

    protected $table = 'bloques';
    protected $primaryKey = 'id_bloque';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function horarios()
    {
        return $this->hasMany(HorarioClase::class, 'id_bloque', 'id_bloque');
    }
}
