<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->bigIncrements('id_bitacora');

            // Quién ejecutó la acción
            $table->foreignId('id_usuario')->constrained('users', 'id');

            // Contexto funcional
            $table->string('accion', 50);                 // p.ej.: CREAR, EDITAR, ELIMINAR, APROBAR, PUBLICAR, MARCAR_ASISTENCIA
            $table->string('modulo', 50)->nullable();     // p.ej.: HORARIOS, ASISTENCIAS, JUSTIFICACIONES, SUPLENCIAS
            $table->string('tabla_afectada', 100);        // p.ej.: horario_clases, asistencias
            $table->integer('registro_id')->nullable(); // id del registro afectado (sin FK para permitir tablas varias)
            $table->text('descripcion')->nullable();      // resumen humano de la acción

            // Enlace a la gestión para filtros de auditoría por periodo
            $table->unsignedInteger('id_gestion')->nullable();    // FK opcional, int porque gestiones usa increments
            $table->foreign('id_gestion')->references('id_gestion')->on('gestiones');

            // Datos técnicos de la petición
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('metodo', 10)->nullable();     // GET/POST/PUT/DELETE
            $table->boolean('exitoso')->default(true);

            // Captura de estado para auditoría (opcional)
            $table->json('metadata')->nullable();         // cualquier dato extra (rol del usuario, scope, etc.)
            $table->json('cambios_antes')->nullable();    // snapshot previo
            $table->json('cambios_despues')->nullable();  // snapshot posterior

            $table->timestamps();

            // Índices útiles para consultas de auditoría
            $table->index(['tabla_afectada', 'registro_id'], 'idx_bitacora_objeto');
            $table->index(['accion', 'created_at'], 'idx_bitacora_accion_fecha');
            $table->index('id_usuario', 'idx_bitacora_usuario');
            $table->index('id_gestion', 'idx_bitacora_gestion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
