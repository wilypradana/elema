<?php

namespace Database\Seeders;

use App\Models\GuruMataPelajaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SlugGuruMataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Fetch all existing GuruMataPelajaran records
         $mataPelajarans = GuruMataPelajaran::all();

         foreach ($mataPelajarans as $mataPelajaran) {
             // Update the slug with a random string
             $mataPelajaran->slug = Str::random(10);
             $mataPelajaran->save();
    }
}
}
