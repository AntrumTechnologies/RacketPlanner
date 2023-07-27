<?php

namespace App\Http\Controllers\Planner;

use App\Models\Schedule;
use App\Models\Round;
use App\Models\Match;

class ScheduleController extends PlannerController
{
    public function show($tournament_id) {
        $schedule = Schedule::where('tournament_id', $tournament_id)->get();
        return $schedule;
    }

    public function generate_schedule($tournament_id) {
        // TODO(PATBRO): prevent generating schedule twice?
        $rounds = Round::where('tournament_id', $tournament_id)->get();
        $courts = Court::where('tournament_id', $tournament_id)->get();

        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $newSchedule = new Schedule ([
                    'tournament_id' => $tournament_id,
                    'court_id' => $court->id,
                    'round_id' => $round->id,
                ]);

                $newSchedule->save();
            }
        }
    }

    public function set_slot_state($slot_id, $state) {
        $schedule = Schedule::where('id', $slot_id)->update(['match_id' => null, 'state' => $state]);
    }

    public function assign_match_to_slot($slot_id, $match_id) {
        $schedule = Schedule::where('id', $slot_id)->update(['match_id' => $match_id, 'state' => 'available']);
    }

    public function delete_match_to_slot($slot_id, $match_id) {
        $schedule = Schedule::where('id', $slot_id)->update(['match_id' => null, 'state' => 'available']);
    }
}
