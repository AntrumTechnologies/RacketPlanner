<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Round;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Models\AdminOrganizationalAssignment;
use Illuminate\Support\Facades\Auth;

class PlannerDatabaseHelper 
{
    public static function RegenerateSchedule($tournamentId) {
        $tournament = Tournament::findOrFail($tournamentId);
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $slots = Schedule::where('tournament_id', $tournamentId)->get();
        $rounds = Round::where('tournament_id', $tournamentId)->get();
        $courts = Court::where('tournament_id', $tournamentId)->get();

        // Create slots for new rounds & courts
        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $exists = false;
                
                foreach ($slots as $slot) {
                    if (($slot->round_id == $round->id) && ($slot->court_id == $court->id)) {
                        $exists = true;
                        break;
                    }
                }
                
                if ($exists == false) {
                    $newSlot = new Schedule([
                        'tournament_id' => $tournamentId,
                        'round_id' => $round->id,
                        'court_id' => $court->id,
                    ]);

                    $newSlot->save();
                }
            }
        }
    }
}