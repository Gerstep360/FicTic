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
        Schema::create('reprogramaciones', function (Blueprint $table) {
            $table->id('id_reprogramacion');
            $table->unsignedInteger('id_horario_original');
            $table->date('fecha_original')->comment('Fecha original de la clase');
            $table->unsignedInteger('id_aula_nueva')->nullable()->comment('Si es cambio de aula');
            $table->date('fecha_nueva')->nullable()->comment('Si es cambio de fecha');
            $table->enum('tipo', ['CAMBIO_AULA', 'CAMBIO_FECHA', 'AMBOS'])->default('CAMBIO_AULA');
            $table->text('motivo')->comment('Razón de la reprogramación');
            $table->enum('estado', ['PENDIENTE', 'APROBADA', 'RECHAZADA'])->default('PENDIENTE');
            $table->unsignedBigInteger('solicitado_por');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->datetime('fecha_solicitud');
            $table->datetime('fecha_aprobacion')->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_horario_original')
                ->references('id_horario')->on('horario_clases')
                ->onDelete('cascade')->onUpdate('cascade');
            
            $table->foreign('id_aula_nueva')
                ->references('id_aula')->on('aulas')
                ->onDelete('set null')->onUpdate('cascade');
            
            $table->foreign('solicitado_por')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            
            $table->foreign('aprobado_por')
                ->references('id')->on('users')
                ->onDelete('set null')->onUpdate('cascade');

            // Índices
            $table->index('estado');
            $table->index('tipo');
            $table->index('fecha_original');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reprogramaciones');
    }
};
