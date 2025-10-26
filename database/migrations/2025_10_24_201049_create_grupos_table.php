<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->bigIncrements('id_grupo');

            $table->string('nombre_grupo', 10);      // p.ej. GR1, A, B
            $table->string('turno', 20);             // Mañana/Tarde/Noche
            $table->string('modalidad', 20);         // Presencial/Virtual/Laboratorio
            $table->unsignedInteger('cupo')->nullable();

            // Claves foráneas
            $table->unsignedBigInteger('id_materia');
            $table->unsignedInteger('id_gestion');
            $table->unsignedBigInteger('id_docente')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Evita duplicados de grupo por materia+gestión
            $table->unique(['id_materia', 'id_gestion', 'nombre_grupo'], 'uniq_grupo_materia_gestion');

            $table->foreign('id_materia')->references('id_materia')->on('materias')->cascadeOnDelete();
            $table->foreign('id_gestion')->references('id_gestion')->on('gestiones')->cascadeOnDelete();
            $table->foreign('id_docente')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
