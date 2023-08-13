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
            $tournamentId = 1;
            if (Route::current() !== null) {
            	$tournamentId = Route::current()->__get('tournamentId');
	    }
	    
            return new Planner($tournamentId);
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
