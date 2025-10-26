<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('justificaciones', function (Blueprint $table) {
            $table->increments('id_justif'); // SERIAL

            $table->foreignId('id_docente')->constrained('users', 'id');
            $table->date('fecha_clase');
            $table->text('motivo');
            $table->string('estado', 15)->default('PENDIENTE'); // PENDIENTE/APROBADA/RECHAZADA

            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_resolucion')->nullable();

            // usuario (coordinador) que aprobó/rechazó
            $table->foreignId('resuelta_por')->nullable()->constrained('users', 'id');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('justificaciones');
    }
};
