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
        Schema::create('hasil_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kuis')->constrained('kuis', 'id')->onDelete('cascade');
            $table->foreignId('id_siswa')->constrained('siswas', 'id')->onDelete('cascade');
            $table->dateTime('waktu_mulai')->nullable(true);
            $table->dateTime('waktu_selesai')->nullable(true);
            $table->string('status')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_kuis');
    }
};
