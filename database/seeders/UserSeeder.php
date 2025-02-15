<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'nama' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_lahir' => 'Jakarta', // Example birthplace
                'tanggal_lahir' => '1990-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Bandung', // Example birthplace
                'tanggal_lahir' => '1992-02-02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Prasastio Giri',
                'email' => 'prasastiog@gmail.com',
                'password' => Hash::make('prasastio291201'),
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_lahir' => 'Mojokerto',
                'tanggal_lahir' => '2001-12-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
