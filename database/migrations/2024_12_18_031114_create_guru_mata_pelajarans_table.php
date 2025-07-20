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
        Schema::create('guru_mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mata_pelajaran');
            $table->unsignedBigInteger('id_guru');
            $table->foreign('id_mata_pelajaran')
                ->references('id')
                ->on('mata_pelajarans')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('id_guru')
                ->references('id')
                ->on('gurus')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unique(['id_guru', 'id_mata_pelajaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_mata_pelajarans');
    }
};
