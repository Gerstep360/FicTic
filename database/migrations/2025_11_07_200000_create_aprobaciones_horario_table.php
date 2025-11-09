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
        Schema::create('aprobaciones_horario', function (Blueprint $table) {
            $table->id('id_aprobacion');
            
            // Alcance de la aprobación
            $table->unsignedInteger('id_gestion');
            $table->unsignedInteger('id_carrera')->nullable(); // null = aprobación facultativa (Decano)
            
            // Estado del proceso
            $table->enum('estado', [
                'borrador',           // Coordinador trabajando
                'pendiente_director', // Enviado a Director
                'observado_director', // Director solicitó cambios
                'aprobado_director',  // Director aprobó
                'pendiente_decano',   // Enviado a Decano (consolidado)
                'observado_decano',   // Decano solicitó cambios
                'aprobado_final',     // Aprobado por Decano (listo para publicar)
                'rechazado',          // Rechazado definitivamente
            ])->default('borrador');
            
            // Información de avance
            $table->integer('total_horarios')->default(0);
            $table->integer('horarios_validados')->default(0);
            $table->integer('conflictos_pendientes')->default(0);
            
            // Auditoría de usuarios que intervinieron
            $table->unsignedBigInteger('id_coordinador')->nullable(); // quien envió
            $table->unsignedBigInteger('id_director')->nullable();    // quien revisó
            $table->unsignedBigInteger('id_decano')->nullable();      // quien aprobó final
            
            // Timestamps de cada transición
            $table->timestamp('fecha_envio_director')->nullable();
            $table->timestamp('fecha_respuesta_director')->nullable();
            $table->timestamp('fecha_envio_decano')->nullable();
            $table->timestamp('fecha_respuesta_decano')->nullable();
            $table->timestamp('fecha_publicacion')->nullable();
            
            // Observaciones en cada nivel
            $table->text('observaciones_director')->nullable();
            $table->text('observaciones_decano')->nullable();
            $table->text('observaciones_coordinador')->nullable(); // respuesta a observaciones
            
            // Metadata adicional
            $table->json('metadata')->nullable(); // validaciones ejecutadas, cambios realizados, etc.
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_gestion')->references('id_gestion')->on('gestiones')->onDelete('cascade');
            $table->foreign('id_carrera')->references('id_carrera')->on('carreras')->onDelete('cascade');
            $table->foreign('id_coordinador')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_director')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_decano')->references('id')->on('users')->onDelete('set null');
            
            // Indices
            $table->index(['id_gestion', 'id_carrera']);
            $table->index('estado');
            
            // Constraint: solo una aprobación activa por gestión-carrera
            $table->unique(['id_gestion', 'id_carrera'], 'uk_aprobacion_gestion_carrera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprobaciones_horario');
    }
};
