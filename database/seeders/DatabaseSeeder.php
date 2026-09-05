<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        // 1. Superadmin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@gmail.com',
            'password' => $password,
            'role' => 'superadmin',
        ]);

        // 2. Management
        User::create([
            'name' => 'Manager Eksekutif',
            'email' => 'management@gmail.com',
            'password' => $password,
            'role' => 'management',
        ]);

        // 3. Finance
        User::create([
            'name' => 'Staff Keuangan',
            'email' => 'finance@gmail.com',
            'password' => $password,
            'role' => 'finance',
        ]);

        // 4. User Biasa
        User::create([
            'name' => 'Client User',
            'email' => 'user@gmail.com',
            'password' => $password,
            'role' => 'user',
        ]);
    }
}
