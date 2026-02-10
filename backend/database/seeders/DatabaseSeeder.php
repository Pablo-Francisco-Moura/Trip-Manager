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
        // Create or update demo admin
        \App\Models\User::updateOrCreate(
            ['email' => 'pablo@example.com'],
            [
                'name' => 'Pablo',
                'is_admin' => true,
                'password' => bcrypt('secret'),
            ]
        );

        // Keep one regular test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
