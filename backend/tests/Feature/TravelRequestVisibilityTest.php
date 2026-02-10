<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelRequestVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_their_own_travel_requests()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        TravelRequest::factory()->create(['user_id' => $userA->id, 'destination' => 'A City']);
        TravelRequest::factory()->create(['user_id' => $userB->id, 'destination' => 'B City']);

        $res = $this->actingAs($userA, 'sanctum')->getJson('/api/travel-requests');

        $res->assertStatus(200);
        $data = $res->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals('A City', $data[0]['destination']);
    }
}
