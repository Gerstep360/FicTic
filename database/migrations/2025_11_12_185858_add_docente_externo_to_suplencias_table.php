<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suplencias', function (Blueprint $table) {
            // Verificar y hacer id_docente_suplente nullable si no lo es
            if (Schema::hasColumn('suplencias', 'id_docente_suplente')) {
                $table->foreignId('id_docente_suplente')->nullable()->change();
            }
            
            // Agregar campo para docente externo solo si no existe
            if (!Schema::hasColumn('suplencias', 'id_docente_externo')) {
                $table->integer('id_docente_externo')->unsigned()->nullable()->after('id_docente_suplente');
                
                // Agregar foreign key
                $table->foreign('id_docente_externo')
                      ->references('id_docente_externo')
                      ->on('docente_externos')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suplencias', function (Blueprint $table) {
            $table->dropForeign(['id_docente_externo']);
            $table->dropColumn('id_docente_externo');
            $table->foreignId('id_docente_suplente')->nullable(false)->change();
        });
    }
};
