<?php

namespace App\Http\Controllers\Planner;

use App\Models\Schedule;
use App\Models\Round;
use App\Models\Match;

class ScheduleController extends PlannerController
{
    public function GetSchedule() {
        Report::Trace(__METHOD__);

        return Schedule::where('tournament_id', $this->tournamentId);;
    }

    public function DeleteSchedule() {
        Report::Trace(__METHOD__);

        $schedule = Schedule::where('tournament_id', $this->tournamentId);
        $schedule->delete();
    }

    public function GenerateSchedule() {
        Report::Trace(__METHOD__);

        $rounds = Round::where('tournament_id', $this->tournamentId)->get();
        $courts = Court::where('tournament_id', $this->tournamentId)->get();

        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $newSlot = new Schedule([
                    "tournament_id" => $this->tournamentId,
                    "court_id" => $court->id,
                    "round_id" => $round->id,
                ]);

                if (!$newSlot->save()) {
                    Report::Fail("Unable to add slot ('$court->id', '$round->id') to schedule.");
                    return false;
                }
            }
        }

        return true;
    }

    public function SetSlotStateToClinic($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'clinic',
                'match_id' => null,
            ]);
    }

    public function SetSlotStateToDisabled($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'disabled',
                'match_id' => null,
            ]);
    }

    public function SetSlotStateToAvailable($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'available',
                'match_id' => null,
            ]);
    }

    public function AssignMatchToSlot($slotId, $matchId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'available',
                'match_id' => $matchId,
            ]);
    }

    public function DeleteMatchFromSlot($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'available',
                'match_id' => null,
            ]);
    }

    public function GetSlotsByRoundId($roundId) {
        Report::Trace(__METHOD__);

        return Schedule::where('tournament_id', $this->tournamentId)->where('round_id', $roundId)->get();
    }
}
