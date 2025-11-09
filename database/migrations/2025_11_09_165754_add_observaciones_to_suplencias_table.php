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
        Schema::table('suplencias', function (Blueprint $table) {
            $table->string('observaciones', 500)->nullable()->after('fecha_clase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suplencias', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }
};
