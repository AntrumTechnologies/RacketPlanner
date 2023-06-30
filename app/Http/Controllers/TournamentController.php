<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\TournamentCourt;
use App\Models\TournamentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{
    public static function index() {
        $tournaments = Tournament::all();

        return view('tournaments', ['tournaments' => $tournaments]);
    }

    public function show($id) {
        $tournament = Tournament::findOrFail($id);

        $tournamentMatches = DB::table('matches')->where('tournament', $id)
            ->leftJoin('users as player1', 'player1.id', '=', 'matches.player1')
            ->leftJoin('users as player2', 'player2.id', '=', 'matches.player2')
            ->leftJoin('users as player3',  function ($f) {
                $f->on('player3.id', '=', 'matches.player3')->whereNotNull('player3.id');
            })
            ->leftJoin('users as player4',  function ($f) {
                $f->on('player4.id', '=', 'matches.player4')->whereNotNull('player4.id');
            })
            ->leftJoin('courts', 'courts.id', '=', 'matches.court')
            ->select('matches.id',
                'courts.name as court',
                'matches.datetime',
                'matches.score1_2',
                'matches.score3_4',
                'player1.name as player1',
                'player1.id as player1_id',
                'player2.name as player2',
                'player2.id as player2_id',
                'player3.name as player3',
                'player3.id as player3_id',
                'player4.name as player4',
                'player4.id as player4_id',)
            ->get();
        $tournamentMatches = $tournamentMatches->groupBy('datetime');

        $tournamentCourts = DB::table('tournaments_courts')->where('tournament', $id)->join('courts', 'courts.id', '=', 'tournaments_courts.court')->get();

        $tournamentUsers = DB::table('tournaments_users')->where('tournament', $id)->join('users', 'users.id', '=', 'tournaments_users.user')->get();

        return view('tournament-details', ['tournament' => $tournament, 'tournamentMatches' => $tournamentMatches, 'tournamentCourts' => $tournamentCourts, 'tournamentUsers' => $tournamentUsers]);
    }

    public function tournamentCourts($id) {
        $tournament = Tournament::findOrFail($id);

        $tournamentCourts = DB::table('tournaments_courts')->where('tournament', $id)->join('courts', 'courts.id', '=', 'tournaments_courts.court')->get();

        $courts = Court::all();

        return view('tournament-courts', ['tournament' => $tournament, 'tournamentCourts' => $tournamentCourts, 'courts' => $courts]);
    }

    public function tournamentUsers($id) {
        $tournament = Tournament::findOrFail($id);

        $tournamentUsers = DB::table('tournaments_users')->where('tournament', $id)->join('users', 'users.id', '=', 'tournaments_users.user')->get();

        $users = User::all();

        return view('tournament-users', ['tournament' => $tournament, 'tournamentUsers' => $tournamentUsers, 'users' => $users]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i:s',
            'datetime_end' => 'required|date_format:Y-m-d H:i:s',
            'matches' => 'required|min:1',
            'duration_m' => 'required|min:1',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'max_diff_rating' => 'sometimes|required|min:0',
            'time_between_matches_m' => 'required|min:0',
		]);

		if ($validator->fails()) {
			return false;
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
        return true;
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
			return false;
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
        return true;
    }

    public function showMatches($id) {
        $tournament = Tournament::findOrFail($id);

        $matches = MatchDetails::where('tournament', $id)->get();
        $matches = $matches->groupBy('datetime');

        $courts = Court::all();

        $users = User::all();
        $users = $users->groupBy('id');

        return view('tournament-matches', ['tournament' => $tournament, 'matches' => $matches, 'courts' => $courts, 'users' => $users]);
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
			return false;
		}

        $tournamentCourt = new TournamentCourt([
            'tournament' => $request->get('tournament'),
            'court' => $request->get('court'),
        ]);

        $tournamentCourt->save();

        return true;
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
			return false;
		}

        $tournamentCourt = new TournamentCourt([
            'tournament' => $request->get('tournament'),
            'user' => $request->get('user'),
        ]);

        $tournamentCourt->save();

        return true;
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:tournaments',
        ]);

		if ($validator->fails()) {
			return false;
		}

        $tournament = Tournament::find($id);
        $tournament->delete();

        return true;
    }
}
