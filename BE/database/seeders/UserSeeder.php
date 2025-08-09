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
        // Create a sample user for testing
        User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create additional sample users
        User::create([
            'name' => 'Nguyễn Văn An',
            'email' => 'an.nguyen@student.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Trần Thị Bình',
            'email' => 'binh.tran@student.com', 
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }
}
