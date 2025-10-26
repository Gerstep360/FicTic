<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    protected $guard_name = 'web'; // si usas el guard web

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function gruposComoDocente()
    {
        return $this->hasMany(\App\Models\Grupo::class, 'id_docente', 'id');
    }

    public function horarioClasesComoDocente()
    {
        return $this->hasMany(\App\Models\HorarioClase::class, 'id_docente', 'id');
    }

    public function asistencias()
    {
        return $this->hasMany(\App\Models\Asistencia::class, 'id_docente', 'id');
    }

    public function justificaciones()
    {
        return $this->hasMany(\App\Models\Justificacion::class, 'id_docente', 'id');
    }

    public function suplenciasComoAusente()
    {
        return $this->hasMany(\App\Models\Suplencia::class, 'id_docente_ausente', 'id');
    }

    public function suplenciasComoSuplente()
    {
        return $this->hasMany(\App\Models\Suplencia::class, 'id_docente_suplente', 'id');
    }
    
    public function ambitos(): HasMany {
        return $this->hasMany(\App\Models\UserAmbito::class);
    }

    public function allowedCarreraIds(): array {
        return $this->ambitos
            ->where('scope_type', \App\Models\Carrera::class)
            ->pluck('scope_id')->unique()->values()->all();
    }

    public function allowedFacultadIds(): array {
        return $this->ambitos
            ->where('scope_type', \App\Models\Facultad::class)
            ->pluck('scope_id')->unique()->values()->all();
    }

    public function hasSelfScope(): bool {
        return $this->ambitos
            ->contains(fn($a) => $a->scope_type === static::class && (int)$a->scope_id === (int)$this->id);
    }
}
