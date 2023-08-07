<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Libraries\Planner;
use Illuminate\Http\Request;

class PlannerWrapperController extends Controller
{
    private Planner $planner;

    public function __construct(Planner $planner)
    {
        $this->middleware('auth');
        $this->planner = $planner;
    }

    public function SetSlotStateToAvailable($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->where('tournament_id', $tournamentId)->update(['state' => 'available', 'match_id' => null]);

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Set slot to available');
    }

    public function SetSlotStateToClinic($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->where('tournament_id', $tournamentId)->update(['state' => 'clinic', 'match_id' => null]);

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Set slot to clinic');
    }

    public function SetSlotStateToDisabled($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->where('tournament_id', $tournamentId)->update(['state' => 'disabled', 'match_id' => null]);

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Set slot to disabled');
    }

    // TODO(PATBRO): wait for edit from JB, then change to only (automatically) run when courts or rounds are added/deleted
    public function GenerateSchedule($tournamentId) {
        $rounds = Round::where('tournament_id', $tournamentId)->get();
        $courts = Court::where('tournament_id', $tournamentId)->get();

        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $newSlot = new Schedule([
                    "tournament_id" => $tournamentId,
                    "court_id" => $court->id,
                    "round_id" => $round->id,
                ]);

                if (!$newSlot->save()) {
                    Report::Fail("Unable to add slot ('$court->id', '$round->id') to schedule.");
                    return false;
                }
            }
        }

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Generated schedule');
    }

    // TODO(PATBRO): only run (automatically) when courts, rounds, or players change
    public function GenerateMatches($tournamentId) {
        $this->planner->GenerateMatches();

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Generated matches');
    }

    public function PlanSlot($tournamentId, $slotId) {
        $this->planner->PlanSlot($slotId);

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Scheduled slot');
    }
    
    public function PlanRound($tournamentId, $roundId) {
        $this->planner->PlanRound($roundId);

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Scheduled round');
    }
    
    public function PlanSchedule($tournamentId) {
        $this->planner->PlanSchedule();

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Scheduled complete tournament');
    }
    
}
