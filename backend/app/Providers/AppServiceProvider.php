<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TravelRequest;
use App\Policies\TravelRequestPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register TravelRequest policy
        Gate::policy(TravelRequest::class, TravelRequestPolicy::class);
    }
}
