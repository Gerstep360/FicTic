<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('aulas', function (Blueprint $table) {
            $table->increments('id_aula'); // SERIAL
            $table->string('codigo', 20);   // "236-08"
            $table->string('tipo', 20);     // "Teórica" / "Laboratorio"
            $table->integer('capacidad')->nullable();
            $table->string('edificio', 50)->nullable(); // nombre o código
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('aulas');
    }
};
