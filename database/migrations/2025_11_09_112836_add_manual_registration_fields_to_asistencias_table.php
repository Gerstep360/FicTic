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
        Schema::table('asistencias', function (Blueprint $table) {
            // Campos para rastrear registro manual
            $table->boolean('es_manual')->default(false)->after('estado')
                  ->comment('Indica si fue registro manual (true) o por QR (false)');
            
            $table->unsignedBigInteger('registrado_por')->nullable()->after('es_manual')
                  ->comment('Usuario que hizo el registro manual');
            
            $table->text('observacion')->nullable()->after('registrado_por')
                  ->comment('Observación o justificación del registro manual');
            
            // Foreign key
            $table->foreign('registrado_por')->references('id')->on('users')
                  ->onDelete('set null')->onUpdate('cascade');
            
            // Índice para búsquedas
            $table->index('es_manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['registrado_por']);
            $table->dropIndex(['es_manual']);
            $table->dropColumn(['es_manual', 'registrado_por', 'observacion']);
        });
    }
};
