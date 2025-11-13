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
        Schema::table('aprobaciones_horario', function (Blueprint $table) {
            // REGLA: Solo una aprobación activa por gestión
            // Permitir múltiples aprobaciones solo si están rechazadas
            // Índice único condicional (solo para estados activos)
            $table->index(['id_gestion', 'estado'], 'idx_gestion_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aprobaciones_horario', function (Blueprint $table) {
            $table->dropIndex('idx_gestion_estado');
        });
    }
};
