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
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function index() {
        $tournaments = Tournament::all();
        
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        foreach ($tournaments as $tournament) {
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
        }

        return view('admin.tournaments', ['tournaments' => $tournaments]);
    }

    public function show($id) {
        $tournament = Tournament::findOrFail($id);
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
        $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));

        $tournamentMatches = DB::table('matches')->where('tournament', $id)
            ->leftJoin('users as player1a', 'player1a.id', '=', 'matches.player1a')
            ->leftJoin('users as player2a', 'player2a.id', '=', 'matches.player2a')
            ->leftJoin('users as player1b',  function ($f) {
                $f->on('player1b.id', '=', 'matches.player1b')->whereNotNull('player1b.id');
            })
            ->leftJoin('users as player2b',  function ($f) {
                $f->on('player2b.id', '=', 'matches.player2b')->whereNotNull('player2b.id');
            })
            ->leftJoin('courts', 'courts.id', '=', 'matches.court')
            ->select('matches.id',
                'courts.name as court',
                'matches.datetime',
                'matches.score1_2',
                'matches.score3_4',
                'player1a.name as player1a',
                'player1a.id as player1a_id',
                'player2a.name as player2a',
                'player2a.id as player2a_id',
                'player1b.name as player1b',
                'player1b.id as player1b_id',
                'player2b.name as player2b',
                'player2b.id as player2b_id',)
            ->get();
        $tournamentMatches = $tournamentMatches->groupBy('datetime');

        $tournamentCourts = DB::table('courts')->where('tournament_id', $id)->get();

        $tournamentUsers = DB::table('players')->where('tournament_id', $id)->join('users', 'users.id', '=', 'players.user_id')->get();

        return view('admin.tournament', ['tournament' => $tournament, 'tournamentMatches' => $tournamentMatches, 'tournamentCourts' => $tournamentCourts, 'tournamentUsers' => $tournamentUsers]);
    }

    public function showDetails($id) {
        $tournament = Tournament::findOrFail($id);
        
        return view('admin.tournament-details', ['tournament' => $tournament]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'matches' => 'required|min:1',
            'duration_m' => 'required|min:1',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'time_between_matches_m' => 'required|min:0',
		]);

        $newTournament = new Tournament([
            'name' => $request->get('name'),
            'datetime_start' => $request->get('datetime_start'),
            'datetime_end' => $request->get('datetime_end'),
            'matches' => $request->get('matches'),
            'duration_m' => $request->get('duration_m'),
            'type' => $request->get('type'),
            'allow_singles' => $request->get('allow_singles'),
            'time_between_matches_m' => $request->get('time_between_matches_m'),
            'created_by' => Auth::id(),
        ]);

        $newTournament->save();
        return Redirect::route('tournaments')->with('status', 'Successfully added the tournament '. $newTournament->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:tournaments',
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'matches' => 'required|min:1',
            'duration_m' => 'required|min:1',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'time_between_matches_m' => 'required|min:0',
		]);

        $tournament = Tournament::find($request->get('id'));
        
        if ($request->has('name')) {
            $tournament->name = $request->get('name');
        }
        
        if ($request->has('datetime_start')) {
            $tournament->datetime_start = $request->get('datetime_start');
        }
        
        if ($request->has('datetime_end')) {
            $tournament->datetime_end = $request->get('datetime_end');
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

        if ($request->has('time_between_matches_m')) {
            $tournament->time_between_matches_m = $request->get('time_between_matches_m');
        }

        $tournament->save();
        return Redirect::route('tournament', ['id' => $tournament->id])->with('status', 'Successfully updated the tournament details');
    }

    public function delete(Request $request) {
        $request->validate([
            'id' => 'required|exists:tournaments',
        ]);

        $tournament = Tournament::find($request->id);
        $tournament->delete();

        // TODO: delete all corresponding matches as well, and assigned users and courts.

        return Redirect::route('tournaments')->with('status', 'Successfully removed the tournament '. $tournament->name);
    }
}
