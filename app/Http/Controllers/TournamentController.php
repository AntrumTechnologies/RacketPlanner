<?php

namespace App\Http\Controllers;

use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\TournamentCourt;
use App\Models\TournamentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{
    public function index() {
        $tournaments = Tournament::all();
        return Response::json($tournaments, 200);
    }

    public function show($id) {
        $tournament = Tournament::findOrFail($id);
        if ($tournament) {
            return Response::json($tournament, 200);
        } else {
            return Response::json("The given tournament could not be found.", 400);
        }
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'matches' => 'required|min:1',
            'duration_m' => 'required|min:1',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'max_diff_rating' => 'sometimes|required|min:0',
            'time_between_matches_m' => 'required|min:0',
		]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}
        
        $newTournament = new Tournament([
            'name' => $request->get('name'),
            'datetime_start' => $request->get('datetime_start'),
            'datetime_end' => $request->get('datetime_end'),
            'matches' => $request->get('matches'),
            'duration_m' => $request->get('duration_m'),
            'type' => $request->get('type'),
            'allow_singles' => $request->get('allow_singles'),
            'max_diff_rating' => $request->get('max_diff_rating'),
            'time_between_matches_m' => $request->get('time_between_matches_m'),
            'created_by' => 1,
        ]);

        $newTournament->save();
        return Response::json("Successfully saved new tournament", 200);
    }

    public function update(Request $request) {
        $$validator = Validator::make($request->all(), [
            'id' => 'required|exists:tournaments',
            'name' => 'sometimes|required|max:50',
            'datetime_start' => 'sometimes|required|date_format:yyyy-mm-dd H:i',
            'datetime_end' => 'sometimes|required|date_format:yyyy-mm-dd H:i',
            'matches' => 'sometimes|required|min:1',
            'duration_m' => 'sometimes|required|min:1',
            'type' => 'sometimes|required', // TODO: define enum class?
            'allow_singles' => 'sometimes|required',
            'max_diff_rating' => 'sometimes|required|min:0',
            'time_between_matches_m' => 'sometimes|required|min:0',
		]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $tournament = Tournament::find($id);
        
        if ($request->has('name')) {
            $tournament->name = $request->get('name');
        }
        
        if ($request->has('datetime_start')) {
            $tournament->datetime_start = $request->get('datetime_start');
        }
        
        if ($request->has('datetime_end')) {
            $tournament->datetime_start = $request->get('availability_end');
        }

        if ($request->has('matches')) {
            $tournament->matches = $request->get('matches');
        }

        if ($request->has('duration_m')) {
            $tournament->duration_m = $request->get('duration_m');
        }

        if ($request->has('type')) {
            $tournament->type = $request->get('type');
        }

        if ($request->has('allow_singles')) {
            $tournament->allow_singles = $request->get('allow_singles');
        }

        if ($request->has('max_diff_rating')) {
            $tournament->max_diff_rating = $request->get('max_diff_rating');
        }

        if ($request->has('time_between_matches_m')) {
            $tournament->time_between_matches_m = $request->get('time_between_matches_m');
        }

        $tournament->save();
        return Response::json("Tournament has been updated successfully", 200);
    }

    public function showMatches($id) {
        $matches = MatchDetails::where('tournament', $id);
        return Response::json($matches, 200);
    }

    /**
     * Add courts to a certain tournament
     */
    public function matchCourts(Request $request) {
        $validator = Validator::make($request->all(), [
            'tournament' => 'required|exists:tournaments',
            'court' => 'required|exists:courts',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $tournamentCourt = new TournamentCourt([
            'tournament' => $request->get('tournament'),
            'court' => $request->get('court'),
        ]);

        $tournamentCourt->save();

        return Response::json("Tournament and court were matched", 200);
    }

    /**
     * Add users to a certain tournament
     */
    public function matchUsers(Request $request) {
        $validator = Validator::make($request->all(), [
            'tournament' => 'required|exists:tournaments',
            'user' => 'required|exists:users',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $tournamentCourt = new TournamentCourt([
            'tournament' => $request->get('tournament'),
            'user' => $request->get('user'),
        ]);

        $tournamentCourt->save();

        return Response::json("Tournament and user were matched", 200);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tournaments',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $tournament = Tournament::find($id);
        $tournament->delete();

        return Response::json("Tournament has been deleted", 200);
    }
}
