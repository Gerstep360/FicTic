<?php

// database/migrations/xxxx_xx_xx_create_user_ambitos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_ambitos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            // ámbito polimórfico: App\Models\Facultad | App\Models\Carrera | App\Models\User (para "propio")
            $t->string('scope_type');               // FQCN del modelo
            $t->unsignedBigInteger('scope_id');     // id del ámbito
            $t->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['user_id','scope_type','scope_id','role_id'], 'ua_unique');
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_ambitos');
    }
};
