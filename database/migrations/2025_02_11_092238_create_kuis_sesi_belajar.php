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
        Schema::create('kuis_sesi_belajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sesi_belajar')->constrained('sesi_belajars', 'id')->onDelete('cascade');
            $table->foreignId('id_kuis')->constrained('kuis', 'id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuis_sesi_belajar');
    }
};
