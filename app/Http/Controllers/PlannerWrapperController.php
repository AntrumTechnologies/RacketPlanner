<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Schedule;
use App\Models\Round;
use App\Models\Player;
use App\Models\Tournament;
use App\Libraries\Planner\Planner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Set slot to available');
    }

    public function SetSlotStateToClinic($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->where('tournament_id', $tournamentId)->update(['state' => 'clinic', 'match_id' => null]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Set slot to clinic');
    }

    public function SetSlotStateToDisabled($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->where('tournament_id', $tournamentId)->update(['state' => 'disabled', 'match_id' => null]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Set slot to disabled');
    }

    public function EmptySlot($tournamentId, $slotId) {
        Schedule::where('id', $slotId)->update(['match_id' => NULL]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Emptied slot');
    }

    public function EmptyAllSlots($tournamentId) {
        Schedule::where('tournament_id', $tournamentId)->update(['match_id' => NULL]);

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Emptied all slots');
    }

    public function PublishRound($tournamentId, $roundId)
    {
        Schedule::where('round_id', $roundId)->where('state', '!=', 'disabled')->update(['public' => 1]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#round'. $roundId)->with('status', 'Published round');
    }

    public function UnpublishRound($tournamentId, $roundId)
    {
        Schedule::where('round_id', $roundId)->update(['public' => 0]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#round'. $roundId)->with('status', 'Unpublished round');
    }

    public function PublishSlot($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->update(['public' => 1]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Published slot');
    }

    public function UnpublishSlot($tournamentId, $slotId)
    {
        Schedule::where('id', $slotId)->update(['public' => 0]);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Unpublished slot');
    }

    // (Automatically) run when courts or rounds are added/deleted
    public function GenerateSchedule($tournamentId) {
        $schedule = Schedule::where('tournament_id', $tournamentId);
        $schedule->delete();
        
        $rounds = Round::where('tournament_id', $tournamentId)->orderBy('id')->get();
        $courts = Court::where('tournament_id', $tournamentId)->orderBy('id')->get();

        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $newSlot = new Schedule([
                    "tournament_id" => $tournamentId,
                    "court_id" => $court->id,
                    "round_id" => $round->id,
                ]);

                if (!$newSlot->save()) {
                    return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Failed to save slot: '. $newSlot);
                }
            }
        }
        
        $this->planner->GenerateMatches();

        $tournament = Tournament::find($tournamentId);
        $tournament->change_to_courts_rounds = false;
        $tournament->change_to_players = false;
        $tournament->save();

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Generated schedule');
    }

    // (Automatically) run when courts, rounds, or players change
    public function GenerateMatches($tournamentId) {
        if (Player::where('tournament_id', $tournamentId)->count() != 0) {
            $this->planner->GenerateMatches();
        }

        $tournament = Tournament::find($tournamentId);
        $tournament->change_to_players = false;
        $tournament->save();

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Generated matches');
    }

    public function PlanSlot($tournamentId, $slotId) {
        $this->planner->PlanSlot($slotId);

        return redirect()->to(route('tournament', ['tournament_id' => $tournamentId]) .'?showround=all#slot'. $slotId)->with('status', 'Scheduled slot');
    }
    
    public function PlanRound($tournamentId, $roundId) {
        $this->planner->PlanRound($roundId);

        $round = Round::findOrFail($roundId);

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Scheduled round: '. $round->name);
    }
    
    public function PlanSchedule($tournamentId) {
        $this->planner->PlanSchedule();

        return Redirect::route('tournament', ['tournament_id' => $tournamentId])->with('status', 'Scheduled complete tournament');
    }
    
}
