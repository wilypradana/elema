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
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->dropColumn('file');
            $table->dropColumn('namafile');
            $table->enum('status_pengumpulan', ["terlambat", "tepat waktu"])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->string('file')->nullable();
            $table->string('namafile')->nullable();
            $table->string('status_pengumpulan')->change();
        });
    }
};
