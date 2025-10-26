<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justificacion extends Model
{
    use HasFactory;

    protected $table = 'justificaciones';
    protected $primaryKey = 'id_justif';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_resolucion'=> 'datetime',
        'fecha_clase'     => 'date',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'id_docente', 'id');
    }

    public function resolutor()
    {
        return $this->belongsTo(User::class, 'resuelta_por', 'id');
    }
}
