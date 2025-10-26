<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carreras', function (Blueprint $table) {
            $table->increments('id_carrera'); // SERIAL
            $table->string('nombre', 100);
            $table->integer('id_facultad')->unsigned();
            $table->foreign('id_facultad')->references('id_facultad')->on('facultades');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('carreras');
    }
};
