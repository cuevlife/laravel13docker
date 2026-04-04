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
        // Admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => bcrypt('123123'),
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 'active',
                'tokens' => 1000,
            ]
        );

        // Regular user
        User::updateOrCreate(
            ['username' => 'user'],
            [
                'name' => 'Regular User',
                'email' => 'user@user.com',
                'email_verified_at' => now(),
                'password' => bcrypt('123123'),
                'role' => User::ROLE_USER,
                'status' => 'active',
                'tokens' => 100,
            ]
        );
    }
}
