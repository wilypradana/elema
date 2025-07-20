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

        DB::table('hasil_kuis')->whereNull('skor')->update(['skor' => 0]);
        Schema::table('hasil_kuis', function (Blueprint $table) {
            $table->double('skor')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_kuis', function (Blueprint $table) {
            $table->double('skor')->default(null)->change();
        });
    }
};
