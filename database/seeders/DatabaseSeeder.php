<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@thermogun.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Petugas Pintu Depan',
            'email' => 'petugas@thermogun.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        User::create([
            'name' => 'Pengguna Umum',
            'email' => 'pengguna@thermogun.com',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
        ]);
    }
}
