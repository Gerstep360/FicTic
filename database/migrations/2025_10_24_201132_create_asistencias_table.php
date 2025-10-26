<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->increments('id_asistencia'); // SERIAL

            $table->foreignId('id_docente')->constrained('users', 'id');
            $table->integer('id_horario')->unsigned();

            $table->timestamp('fecha_hora')->useCurrent(); // DEFAULT CURRENT_TIMESTAMP
            $table->string('tipo_marca', 10)->default('ENTRADA'); // ENTRADA/SALIDA (opcional)
            $table->string('estado', 15)->default('PRESENTE');     // PRESENTE/AUSENTE/JUSTIFICADA

            $table->foreign('id_horario')->references('id_horario')->on('horario_clases');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('asistencias');
    }
};
