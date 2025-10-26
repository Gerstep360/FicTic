<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feriados', function (Blueprint $table) {
            $table->increments('id_feriado');      // SERIAL
            $table->unsignedInteger('id_gestion'); // FK a gestiones
            $table->date('fecha');
            $table->string('descripcion', 120)->nullable();
            $table->timestamps();

            $table->foreign('id_gestion')
                  ->references('id_gestion')->on('gestiones')
                  ->onDelete('cascade');

            $table->unique(['id_gestion','fecha'], 'uq_feriados_gestion_fecha');
            $table->index('id_gestion', 'idx_feriados_gestion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
