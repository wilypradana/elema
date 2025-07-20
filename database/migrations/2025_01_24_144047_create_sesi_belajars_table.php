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
        Schema::create('sesi_belajars', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable(false);
            $table->string('slug')->nullable(false);
            $table->foreignId('id_guru_mata_pelajaran')->constrained('guru_mata_pelajarans', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_belajars');
    }
};
