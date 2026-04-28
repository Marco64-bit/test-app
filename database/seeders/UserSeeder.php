<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. The Normal User (Good for testing successful login, profile updates, and links)
        User::create([
            'full_name' => 'Normal Tester',
            'address' => '123 Main St',
            'country' => 'Egypt',
            'email' => 'normal@test.com',
            'password' => Hash::make('Password123'),
            'failed_login_attempts' => 0,
            'is_blocked' => false,
        ]);

        // 2. The "3 Strikes" User (Good for testing the 4th attempt verification route)
        User::create([
            'full_name' => 'Verify Me Tester',
            'address' => '456 Warning Ave',
            'country' => 'Egypt',
            'email' => 'verify@test.com',
            'password' => Hash::make('Password123'),
            'failed_login_attempts' => 3,
            'verification_code' => '123456', // Hardcoded so you don't have to check email to test it!
            'is_blocked' => false,
        ]);

        // 3. The Blocked User (Good for testing that blocked users cannot log in)
        User::create([
            'full_name' => 'Blocked Tester',
            'address' => '789 Locked Blvd',
            'country' => 'Egypt',
            'email' => 'blocked@test.com',
            'password' => Hash::make('Password123'),
            'failed_login_attempts' => 4,
            'is_blocked' => true,
        ]);
    }
}
