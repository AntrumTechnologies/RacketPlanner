<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Round;
use App\Models\Schedule;

class PlannerDatabaseHelper 
{
    public static function RegenerateSchedule($tournamentId) {
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