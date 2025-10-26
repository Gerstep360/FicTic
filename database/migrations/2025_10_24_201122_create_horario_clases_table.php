<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('horario_clases', function (Blueprint $table) {
            $table->increments('id_horario'); // SERIAL

            // 1=Lun ... 7=Dom (normalmente 1-6)
            $table->smallInteger('dia_semana');

            $table->integer('id_bloque')->unsigned();
            $table->integer('id_aula')->unsigned();
            $table->unsignedBigInteger('id_grupo');

            $table->foreignId('id_docente')
                  ->constrained('users', 'id');

            // Unicidades para evitar choques
            $table->unique(['dia_semana', 'id_bloque', 'id_aula'], 'uq_horario_aula_tiempo');
            $table->unique(['dia_semana', 'id_bloque', 'id_docente'], 'uq_horario_docente_tiempo');

            // FKs
            $table->foreign('id_bloque')->references('id_bloque')->on('bloques');
            $table->foreign('id_aula')->references('id_aula')->on('aulas');
            $table->foreign('id_grupo')->references('id_grupo')->on('grupos');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('horario_clases');
    }
};
