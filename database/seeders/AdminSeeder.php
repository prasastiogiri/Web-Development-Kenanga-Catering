<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Jangan lupa, password-nya di-hash
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
