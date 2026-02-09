<?php

namespace Database\Factories;

use App\Models\TravelRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TravelRequestFactory extends Factory
{
    protected $model = TravelRequest::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+2 months');
        $end = (clone $start)->modify('+'.rand(1,7).' days');

        return [
            'user_id' => null,
            'requester_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            'departure_date' => $start->format('Y-m-d'),
            'return_date' => $end->format('Y-m-d'),
            'status' => 'requested',
        ];
    }
}
