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
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tugas')->constrained('tugas', 'id')->onDelete('cascade');
            $table->foreignId('id_siswa')->constrained('siswas', 'id')->onDelete('cascade');
            $table->string('file')->nullable(true);
            $table->integer('nilai')->nullable(true);
            $table->string('status_pengumpulan')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
