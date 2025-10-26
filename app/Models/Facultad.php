<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    use HasFactory;

    protected $table = 'facultades';
    protected $primaryKey = 'id_facultad';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function carreras()
    {
        return $this->hasMany(Carrera::class, 'id_facultad', 'id_facultad');
    }
}
