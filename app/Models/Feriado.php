<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feriado extends Model
{
    use HasFactory;

    protected $table = 'feriados';
    protected $primaryKey = 'id_feriado';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_gestion',
        'fecha',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }
}
