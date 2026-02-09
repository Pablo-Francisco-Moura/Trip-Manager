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
        // Create a demo/admin user for development
        // Password: secret
        \App\Models\User::factory()->create([
            'name' => 'Pablo',
            'email' => 'pablo@example.com',
            'is_admin' => true,
            'password' => bcrypt('secret'),
        ]);

        // Keep one regular test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
