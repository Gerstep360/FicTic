<?php
// app/Models/UserAmbito.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Permission\Models\Role;

class UserAmbito extends Model
{
    protected $table = 'user_ambitos';
    protected $fillable = ['user_id','role_id','scope_type','scope_id'];

    // Si tu PK es 'id', no hace falta definir $primaryKey.

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role(): BelongsTo
    {
        // Spatie\Permission\Models\Role
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function scope(): MorphTo
    {
        // Polimórfica: puede ser User, Carrera o Facultad
        return $this->morphTo(__FUNCTION__, 'scope_type', 'scope_id');
    }
}
