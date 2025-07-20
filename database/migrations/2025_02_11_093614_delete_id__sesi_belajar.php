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
        Schema::table('kuis', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['id_sesi_belajar']);
            // Hapus kolom id_sesi_belajar
            $table->dropColumn('id_sesi_belajar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            // Tambahkan kembali kolom id_sesi_belajar
            $table->foreignId('id_sesi_belajar')->nullable()->constrained('sesi_belajars')->onDelete('cascade');
        });
    }
};
