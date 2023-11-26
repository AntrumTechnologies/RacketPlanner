<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Http\Controllers\PlannerWrapperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CourtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($court_id) {
        $court = Court::findOrFail($court_id);

        return view('admin.court', ['court' => $court]);
    }

    public function store(Request $request) {
        $request->validate([
			'name' => 'required|max:30',
            'tournament_id' => 'required|exists:tournaments,id',
		]);

        $newCourt = new Court([
            'name' => $request->get('name'),
            'tournament_id' => $request->get('tournament_id'),
            'created_by' => Auth::id(),
        ]);

        $newCourt->save();

        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_courts_rounds = true;
        $tournament->save();

        return Redirect::route('courts-rounds', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully added the court '. $newCourt->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:courts',
            'name' => 'sometimes|required|max:30',
            'tournament_id' => 'sometimes|required|exists:tournaments,id',
        ]);

        $court = Court::find($request->get('id'));
        
        if ($request->has('name')) {
            $court->name = $request->get('name');
        }

        if ($request->has('tournament_id')) {
            $court->tournament_id = $request->get('tournament_id');
        }

        $court->save();
        
        return Redirect::route('courts-rounds', ['tournament_id' => $court->tournament_id])
            ->with('status', 'Successfully updated court details for '. $court->name);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:courts',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $schedule = Schedule::where('tournament_id', $request->get('tournament_id'))
            ->where('court_id', $request->get('id'))
            ->delete();

        $court = Court::find($request->get('id'));
        $court->delete();

        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_courts_rounds = true;
        $tournament->save();

        return Redirect::route('courts-rounds', ['tournament_id' => $court->tournament_id])
            ->with('status', 'Successfully deleted court '. $court->name);
    }
}
