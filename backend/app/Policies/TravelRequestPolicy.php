<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;

class TravelRequestPolicy
{
    /**
     * Determine whether the user can view the travel request.
     */
    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can update status of the travel request.
     */
    public function updateStatus(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_admin;
    }
}
