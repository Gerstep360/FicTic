<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bloques', function (Blueprint $table) {
            $table->increments('id_bloque'); // SERIAL
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('etiqueta', 20)->nullable(); // "B1"
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bloques');
    }
};
