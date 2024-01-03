<?php

namespace App\Providers;

use App\Models\Tournament;
use App\Libraries\Planner\Planner;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class PlannerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(Planner::class, function ($app) {
            $tournamentId = 1;
            $desiredNumberOfIterations = 20;
            $desiredPartnerRatingTolerance = 10;
            $desiredTeamRatingTolerance = 4;
            $doubleMatches = true;
            $playerMaxMatchCount = 5;

            if (Route::current() !== null) {
            	$tournamentId = Route::current()->__get('tournamentId');

                $tournament = Tournament::find($tournamentId);
                if ($tournament) {
                    $desiredNumberOfIterations = $tournament->number_of_matches;
                    $desiredPartnerRatingTolerance = $tournament->partner_rating_tolerance;
                    $desiredTeamRatingTolerance = $tournament->team_rating_tolerance;
                    $doubleMatches = $tournament->double_matches;
                    $playerMaxMatchCount = $tournament->max_match_count;
                }
            }

            return new Planner($tournamentId, 
                $desiredNumberOfIterations, 
                $desiredPartnerRatingTolerance, 
                $desiredTeamRatingTolerance,
                $doubleMatches,
                $playerMaxMatchCount);
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
