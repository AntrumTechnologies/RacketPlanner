<?php

namespace App\Http\Controllers;

use App\Models\Round;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Http\Controllers\PlannerDatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class RoundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($round_id) {
        $round = Round::findOrFail($round_id); // Will be only one results anyway due to ID
        
        return view('admin.round', ['round' => $round]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:30',
            'tournament_id' => 'required|exists:tournaments,id',
            'starttime' => 'required|date_format:H:i',
        ]);

        $new_round = new Round([
            'name' => $request->get('name'),
            'tournament_id' => $request->get('tournament_id'),
            'starttime' => $request->get('starttime'),
        ]);

        $new_round->save();

        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_courts_rounds = true;
        $tournament->save();

        return Redirect::route('courts-rounds', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully added the round '. $new_round->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:rounds',
            'name' => 'required|max:30',
            'starttime' => 'required|date_format:H:i',
        ]);

        $round = Round::find($request->get('id'));
        
        if ($request->has('name')) {
            $round->name = $request->get('name');
        }

        if ($request->has('starttime')) {
            $round->starttime = $request->get('starttime');
        }

        $round->save();
        
        return Redirect::route('courts-rounds', ['tournament_id' => $round->tournament_id])
            ->with('status', 'Successfully updated round details for '. $round->name);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:rounds',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $schedule = Schedule::where('tournament_id', $request->get('tournament_id'))
            ->where('round_id', $request->get('id'))
            ->delete();

        $round = Round::find($request->get('id'));
        $round->delete();

        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_courts_rounds = true;
        $tournament->save();

        return Redirect::route('courts-rounds', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully deleted the round '. $round->name);
    }
}
