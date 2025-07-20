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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable(false)->unique();
            $table->string('nama')->nullable(false);
            $table->foreignId("id_jurusan")->constrained("jurusans", "id")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_angkatan')->constrained('angkatans', 'id')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
