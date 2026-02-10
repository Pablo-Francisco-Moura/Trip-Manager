<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_written_to_database_on_status_change()
    {
        Notification::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson('/api/travel-requests/'.$request->id.'/status', ['status' => 'approved'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'approved']);

        Notification::assertSentTo($user, TravelRequestStatusChanged::class);
    }

    public function test_notification_on_cancel_before_approval()
    {
        Notification::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $request = TravelRequest::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson('/api/travel-requests/'.$request->id.'/status', ['status' => 'canceled'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'canceled']);

        Notification::assertSentTo($user, TravelRequestStatusChanged::class);
    }
}
