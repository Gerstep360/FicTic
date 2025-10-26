<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;

    protected $table = 'gestiones';
    protected $primaryKey = 'id_gestion';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_gestion', 'id_gestion');
    }
    public function feriados() {
        return $this->hasMany(Feriado::class, 'id_gestion', 'id_gestion');
    }
}
