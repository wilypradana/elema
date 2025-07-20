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
        Schema::create('kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sesi_belajar')->nullable(true)->constrained('sesi_belajars', 'id')->onDelete('cascade');
            $table->string('judul')->nullable(false);
            $table->text('deskripsi')->nullable(true);
            $table->boolean('aktif')->default(false);
            $table->integer('durasi')->nullable(true);
            $table->datetime('waktu_mulai')->nullable(true);
            $table->datetime('waktu_selesai')->nullable(true);
            $table->boolean('acak_soal')->default(false);
            $table->float('nilai_minimal')->default(0);
            $table->string('slug')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuis');
    }
};
