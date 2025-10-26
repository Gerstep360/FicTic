<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('suplencias', function (Blueprint $table) {
            $table->increments('id_suplencia'); // SERIAL

            $table->foreignId('id_docente_ausente')->constrained('users', 'id');
            $table->foreignId('id_docente_suplente')->constrained('users', 'id');

            $table->integer('id_horario')->unsigned();
            $table->date('fecha_clase');

            $table->foreign('id_horario')->references('id_horario')->on('horario_clases');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('suplencias');
    }
};
