<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_travel_request()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/travel-requests', [
                'requester_name' => 'Fulano',
                'destination' => 'São Paulo',
                'departure_date' => now()->addDays(5)->toDateString(),
                'return_date' => now()->addDays(7)->toDateString(),
            ])
            ->assertStatus(201)
            ->assertJsonFragment(['destination' => 'São Paulo']);
    }

    public function test_non_admin_cannot_update_status()
    {
        $user = User::factory()->create();
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->patchJson('/api/travel-requests/'.$request->id.'/status', ['status' => 'approved'])
            ->assertStatus(403);
    }

    public function test_admin_can_approve_and_cannot_cancel_when_approved()
    {
        Notification::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson('/api/travel-requests/'.$request->id.'/status', ['status' => 'approved'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'approved']);

        // Notification should be sent to requester
        Notification::assertSentTo($request->user, TravelRequestStatusChanged::class);

        $this->actingAs($admin, 'sanctum')
            ->patchJson('/api/travel-requests/'.$request->id.'/status', ['status' => 'canceled'])
            ->assertStatus(422);
    }
}
