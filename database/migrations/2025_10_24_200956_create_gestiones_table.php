<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gestiones', function (Blueprint $table) {
            $table->increments('id_gestion'); // SERIAL
            $table->string('nombre', 50); // Ej. "II-2025"
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('horarios')->default('BORRADOR');
            $table->boolean('publicada')->default(false); // indica si ya publicó horarios
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('gestiones');
    }
};
