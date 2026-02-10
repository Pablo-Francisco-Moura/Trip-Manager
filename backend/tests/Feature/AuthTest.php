<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_token_and_user()
    {
        $res = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'secret',
        ]);

        $res->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_login_returns_token()
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret'),
        ]);

        $res = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'secret',
        ]);

        $res->assertStatus(200)
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);
    }

    public function test_logout_revokes_token()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/logout');
        $res->assertStatus(200)->assertJson(['message' => 'Logout realizado']);
    }
}
