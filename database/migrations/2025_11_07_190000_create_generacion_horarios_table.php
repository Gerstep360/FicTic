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
        Schema::create('generacion_horarios', function (Blueprint $table) {
            $table->id('id_generacion');
            
            // Alcance de la generación
            $table->unsignedInteger('id_gestion');
            $table->foreign('id_gestion')->references('id_gestion')->on('gestiones')->onDelete('cascade');
            
            $table->unsignedInteger('id_carrera')->nullable()->comment('Si es null, genera para toda la facultad');
            $table->foreign('id_carrera')->references('id_carrera')->on('carreras')->onDelete('cascade');
            
            // Usuario que solicitó la generación
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade');
            
            // Configuración de optimización (JSON)
            // Ejemplo: {
            //   "minimizar_huecos": true,
            //   "balancear_carga_diaria": true,
            //   "respetar_preferencias": true,
            //   "preferir_manana": ["docente_id_1", "docente_id_2"],
            //   "preferir_tarde": ["docente_id_3"],
            //   "max_horas_dia_docente": 4,
            //   "min_descanso_entre_clases": 15
            // }
            $table->json('configuracion')->nullable()->comment('Criterios de optimización');
            
            // Estado del proceso
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'error', 'aplicado'])->default('pendiente');
            
            // Resultados
            $table->json('resultado')->nullable()->comment('Horarios generados (array de asignaciones)');
            $table->text('mensaje')->nullable()->comment('Mensajes de error o advertencias');
            
            // Métricas de optimización
            $table->integer('total_grupos')->default(0);
            $table->integer('grupos_asignados')->default(0);
            $table->integer('conflictos_detectados')->default(0);
            $table->decimal('puntuacion_optimizacion', 5, 2)->nullable()->comment('Score de calidad 0-100');
            
            // Duración del proceso
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->integer('duracion_segundos')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['id_gestion', 'id_carrera']);
            $table->index('estado');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generacion_horarios');
    }
};
