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
        Schema::table('gestiones', function (Blueprint $table) {
            // Solo agregar campos si no existen
            if (!Schema::hasColumn('gestiones', 'fecha_publicacion')) {
                $table->timestamp('fecha_publicacion')->nullable()->after('publicada');
            }
            if (!Schema::hasColumn('gestiones', 'publicada_por')) {
                $table->unsignedBigInteger('publicada_por')->nullable()->after('fecha_publicacion');
                $table->foreign('publicada_por')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('gestiones', 'nota_publicacion')) {
                $table->text('nota_publicacion')->nullable()->after('publicada_por');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gestiones', function (Blueprint $table) {
            $table->dropForeign(['publicada_por']);
            $table->dropColumn(['publicada', 'fecha_publicacion', 'publicada_por', 'nota_publicacion']);
        });
    }
};
