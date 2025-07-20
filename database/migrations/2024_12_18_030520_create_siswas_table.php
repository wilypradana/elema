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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable(false)->unique();
            $table->string('name')->nullable(false);
            $table->string('email')->nullable(true)->unique();
            $table->string('password')->nullable(true);
            $table->enum('jenis_kelamin',['l','p'])->nullable(false);
            // Ubah id_kelas menjadi nullable
            $table->foreignId('id_kelas')->nullable()
                ->constrained('kelas', 'id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
