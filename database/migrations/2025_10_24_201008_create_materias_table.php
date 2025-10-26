<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id('id_materia');
            $table->string('codigo', 20);
            $table->string('nombre', 100);
            $table->string('nivel', 20)->default('Licenciatura');
            $table->unsignedInteger('creditos');
            $table->unsignedInteger('id_carrera');
            $table->timestamps();
            $table->softDeletes();

            // Un código único por carrera (respeta soft deletes a nivel DB igual).
            $table->unique(['id_carrera', 'codigo'], 'materias_carrera_codigo_unique');

            $table->foreign('id_carrera')
                ->references('id_carrera')->on('carreras')
                ->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
