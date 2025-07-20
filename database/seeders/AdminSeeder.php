<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sugianto', // Change this to your desired name
            'email' => 'otoytu@gmail.com', // Change this to your desired email
            'password' => bcrypt('password'), // Change this to your desired password
        ]);
    }
}
