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
        Schema::table('sesi_belajars', function (Blueprint $table) {
            // Tambahkan kolom id_kelas sebagai nullable dulu untuk data existing
            $table->unsignedBigInteger('id_kelas')->nullable()->after('id_guru_mata_pelajaran');
            
            // Tambahkan foreign key constraint
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_belajars', function (Blueprint $table) {
            // Drop foreign key constraint terlebih dahulu
            $table->dropForeign(['id_kelas']);
            // Kemudian drop kolom
            $table->dropColumn('id_kelas');
        });
    }
};