<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelRequestController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // authenticated user profile
    Route::get('user', [AuthController::class, 'me']);

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('travel-requests', [TravelRequestController::class, 'index']);
    Route::post('travel-requests', [TravelRequestController::class, 'store']);
    Route::get('travel-requests/{travelRequest}', [TravelRequestController::class, 'show']);
    Route::patch('travel-requests/{travelRequest}/status', [TravelRequestController::class, 'updateStatus']);
    // Admin helpers
    Route::post('users/{id}/make-admin', [\App\Http\Controllers\Api\UserAdminController::class, 'makeAdmin']);
});
