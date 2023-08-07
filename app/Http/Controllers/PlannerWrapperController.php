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

    public function PlanSlot($tournamentId, $slotId) {
        $this->planner->PlanSlot($slotId);

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Scheduled slot');
    }
    
    public function PlanRound($tournamentId, $slotId) {
        $this->planner->PlanRound($slotId);

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Scheduled round');
    }
    
    public function PlanSchedule($tournamentId) {
        $this->planner->PlanSchedule();

        return Redirect::route('tournament', ['id' => $tournamentId])->with('status', 'Scheduled complete tournament');
    }
    
}
