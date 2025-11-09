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
        Schema::create('docente_qr_tokens', function (Blueprint $table) {
            $table->id('id_qr_token');
            $table->foreignId('id_docente')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('id_gestion');
            $table->foreign('id_gestion')->references('id_gestion')->on('gestiones')->onDelete('cascade');
            $table->string('token', 64)->unique()->comment('Token cifrado único para QR');
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->timestamp('fecha_expiracion')->nullable()->comment('Opcional: expiración del QR');
            $table->integer('veces_usado')->default(0)->comment('Contador de escaneos');
            $table->timestamp('ultimo_uso')->nullable();
            $table->timestamps();
            
            // Índices
            $table->unique(['id_docente', 'id_gestion'], 'uk_docente_gestion');
            $table->index('token');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_qr_tokens');
    }
};
