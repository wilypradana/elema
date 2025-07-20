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
        Schema::create('file_pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumpulan_tugas_id')->constrained('pengumpulan_tugas', 'id')->cascadeOnDelete();
            $table->string('file')->nullable(true);
            $table->string('nama_file')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_pengumpulan_tugas');
    }
};
