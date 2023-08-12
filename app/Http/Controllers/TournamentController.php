<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Round;
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
        
        foreach ($tournaments as $tournament) {
            // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
            
            $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());
        }

        return view('tournaments', ['tournaments' => $tournaments]);
    }

    public function show($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
        $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));

        $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());

        $players = Player::where('tournament_id', $tournament_id)
            ->join('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();

        if (Auth::user()->can('admin')) {
            $schedule = Schedule::where('schedules.tournament_id', $tournament_id)
                ->join('rounds', 'rounds.id', '=', 'schedules.round_id')
                ->join('courts', 'courts.id', '=', 'schedules.court_id')
                ->leftJoin('matches', 'matches.id', '=', 'schedules.match_id')
                ->leftJoin('players as player1a', 'matches.player1a_id', '=', 'player1a.id')
                ->leftJoin('players as player1b', 'matches.player1b_id', '=', 'player1b.id')
                ->leftJoin('players as player2a', 'matches.player2a_id', '=', 'player2a.id')
                ->leftJoin('players as player2b', 'matches.player2b_id', '=', 'player2b.id')
                ->leftJoin('users as user1a', 'player1a.user_id', '=', 'user1a.id')
                ->leftJoin('users as user1b', 'player1b.user_id', '=', 'user1b.id')
                ->leftJoin('users as user2a', 'player2a.user_id', '=', 'user2a.id')
                ->leftJoin('users as user2b', 'player2b.user_id', '=', 'user2b.id')
                ->select(
                    'schedules.id as schedule_id',
                    'schedules.public as public',
                    'schedules.state as state',
                    'rounds.id as round_id',
                    'rounds.name as round',
                    'rounds.starttime as time',
                    'courts.name as court',
                    'matches.id',
                    'matches.score1',
                    'matches.score2',
                    'user1a.name as player1a',
                    'user1a.id as player1a_id',
                    'user1a.avatar as player1a_avatar',
                    'user1b.name as player1b',
                    'user1b.id as player1b_id',
                    'user1b.avatar as player1b_avatar',
                    'user2a.name as player2a',
                    'user2a.id as player2a_id',
                    'user2a.avatar as player2a_avatar',
                    'user2b.name as player2b',
                    'user2b.id as player2b_id',
                    'user2b.avatar as player2b_avatar')
                ->orderBy('time', 'asc')
                ->get();
        } else {
            $schedule = Schedule::where('schedules.tournament_id', $tournament_id)
                ->where('schedules.state', '!=', 'disabled') // Hide disabled courts
                ->where('schedules.public', '=', '1') // Only show published matches
                ->join('rounds', 'rounds.id', '=', 'schedules.round_id')
                ->join('courts', 'courts.id', '=', 'schedules.court_id')
                ->leftJoin('matches', 'matches.id', '=', 'schedules.match_id')
                ->leftJoin('players as player1a', 'matches.player1a_id', '=', 'player1a.id')
                ->leftJoin('players as player1b', 'matches.player1b_id', '=', 'player1b.id')
                ->leftJoin('players as player2a', 'matches.player2a_id', '=', 'player2a.id')
                ->leftJoin('players as player2b', 'matches.player2b_id', '=', 'player2b.id')
                ->leftJoin('users as user1a', 'player1a.user_id', '=', 'user1a.id')
                ->leftJoin('users as user1b', 'player1b.user_id', '=', 'user1b.id')
                ->leftJoin('users as user2a', 'player2a.user_id', '=', 'user2a.id')
                ->leftJoin('users as user2b', 'player2b.user_id', '=', 'user2b.id')
                ->select(
                    'schedules.id as schedule_id',
                    'schedules.public as public',
                    'schedules.state as state',
                    'rounds.id as round_id',
                    'rounds.name as round',
                    'rounds.starttime as time',
                    'courts.name as court',
                    'matches.id',
                    'matches.score1',
                    'matches.score2',
                    'user1a.name as player1a',
                    'user1a.id as player1a_id',
                    'user1a.avatar as player1a_avatar',
                    'user1b.name as player1b',
                    'user1b.id as player1b_id',
                    'user1b.avatar as player1b_avatar',
                    'user2a.name as player2a',
                    'user2a.id as player2a_id',
                    'user2a.avatar as player2a_avatar',
                    'user2b.name as player2b',
                    'user2b.id as player2b_id',
                    'user2b.avatar as player2b_avatar')
                ->orderBy('time', 'asc')
                ->get();
        }

        $schedule_clinic = Player::where('tournament_id', $tournament_id)
            ->where('clinic', 1)
            ->join('users', 'users.id', '=', 'players.user_id')
            ->select(
                'users.name as user_name',
                'users.id as user_id')
            ->orderBy('users.name', 'asc')
            ->get();

        $next_round_id = 0;
        foreach ($schedule as $match) {
            if ($match->id == null && $match->state == "available") {
                $next_round_id = $match->round_id;
                break;
            }
        }

        // Update datetime to human readable string which is nice to the eye
        foreach ($schedule as $match) {
            $match->time = date('H:i', strtotime($match->time));
        }

        $courts = Court::where('tournament_id', $tournament_id)->get();
        $rounds = Round::where('tournament_id', $tournament_id)->get();

        foreach ($rounds as $round) {
            $round->starttime = date('H:i', strtotime($round->starttime));
            $round->endtime = date('H:i', strtotime($round->endtime));
        }

        return view('tournament', [
            'tournament' => $tournament, 
            'schedule' => $schedule, 
            'schedule_clinic' => $schedule_clinic,
            'next_round_id' => $next_round_id,
            'players' => $players, 
            'courts' => $courts, 
            'rounds' => $rounds,
        ]);
    }

    public function edit($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);

        return view('admin.tournament-edit', ['tournament' => $tournament]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
		]);

        $newTournament = new Tournament([
            'name' => $request->get('name'),
            'datetime_start' => $request->get('datetime_start'),
            'datetime_end' => $request->get('datetime_end'),
            'type' => $request->get('type'),
            'allow_singles' => $request->get('allow_singles'),
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
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
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

        if ($request->has('type')) {
            $tournament->type = $request->get('type');
        }

        if ($request->has('allow_singles')) {
            $tournament->allow_singles = $request->get('allow_singles');
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
