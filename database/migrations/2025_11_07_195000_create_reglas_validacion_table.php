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
        Schema::create('reglas_validacion', function (Blueprint $table) {
            $table->id('id_regla');
            
            // Alcance de la regla
            $table->unsignedInteger('id_facultad')->nullable();
            $table->foreign('id_facultad')->references('id_facultad')->on('facultades')->onDelete('cascade');
            
            $table->unsignedInteger('id_carrera')->nullable();
            $table->foreign('id_carrera')->references('id_carrera')->on('carreras')->onDelete('cascade');
            
            // Identificador de la regla
            $table->string('codigo', 50)->comment('Código único de la regla (ej: MAX_HORAS_DIA)');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            
            // Configuración
            $table->enum('categoria', [
                'carga_docente',
                'descanso',
                'tipo_aula',
                'capacidad',
                'continuidad',
                'preferencias',
                'otras'
            ])->default('otras');
            
            $table->enum('severidad', ['critica', 'alta', 'media', 'baja'])->default('media')
                ->comment('Nivel de severidad de la violación');
            
            $table->boolean('activa')->default(true);
            $table->boolean('bloqueante')->default(false)
                ->comment('Si es true, impide aplicar horarios con esta violación');
            
            // Parámetros de la regla (JSON)
            // Ejemplo: {"max_horas_dia": 4, "min_descanso_minutos": 30}
            $table->json('parametros')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['id_facultad', 'id_carrera']);
            $table->index('codigo');
            $table->index('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_validacion');
    }
};
