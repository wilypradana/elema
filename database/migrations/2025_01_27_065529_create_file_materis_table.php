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
        Schema::create('file_materis', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable(true);
            $table->string('file')->nullable(true);
            $table->foreignId('id_sesi_belajar')->constrained('sesi_belajars', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_materis');
    }
};
