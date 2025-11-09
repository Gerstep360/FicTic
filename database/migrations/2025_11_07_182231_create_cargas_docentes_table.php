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
        Schema::create('cargas_docentes', function (Blueprint $table) {
            $table->id('id_carga');
            
            // Relaciones
            $table->foreignId('id_docente')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('id_gestion');
            $table->foreign('id_gestion')->references('id_gestion')->on('gestiones')->onDelete('cascade');
            $table->unsignedInteger('id_carrera')->nullable();
            $table->foreign('id_carrera')->references('id_carrera')->on('carreras')->onDelete('cascade');
                        
            // Carga horaria
            $table->integer('horas_contratadas')->comment('Horas totales contratadas por semana');
            $table->integer('horas_asignadas')->default(0)->comment('Horas ya asignadas en horarios');
            $table->string('tipo_contrato', 50)->nullable()->comment('Tiempo completo, medio tiempo, hora cátedra, etc.');
            $table->string('categoria', 50)->nullable()->comment('Titular, Adjunto, Jefe de Trabajos Prácticos, etc.');
            
            // Restricciones de disponibilidad (JSON)
            // Ejemplo: {"lunes": ["07:00-09:00"], "martes": ["14:00-16:00"]}
            $table->json('restricciones_horario')->nullable()->comment('Días/horas en que NO puede dictar');
            
            // Notas adicionales
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['id_docente', 'id_gestion']);
            $table->unique(['id_docente', 'id_gestion', 'id_carrera'], 'uk_docente_gestion_carrera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargas_docentes');
    }
};
