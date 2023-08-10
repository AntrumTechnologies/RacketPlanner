<?php

namespace App\Providers;

use App\Libraries\Planner\Planner;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PlannerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(Planner::class, function ($app) {
            return new Planner(Route::current()->__get('tournamentId'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
