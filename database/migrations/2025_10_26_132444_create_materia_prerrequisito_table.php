<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materia_prerrequisitos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_requisito');

            $table->primary(['id_materia','id_requisito'], 'materia_req_pk');

            $table->foreign('id_materia')
                ->references('id_materia')->on('materias')
                ->cascadeOnDelete();

            $table->foreign('id_requisito')
                ->references('id_materia')->on('materias')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materia_prerrequisitos');
    }
};
