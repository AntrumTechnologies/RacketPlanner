<?php

namespace App\Http\Controllers;

use App\Models\MatchDetails;
use App\Models\Player;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MatchDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $matches = MatchDetails::all();
        return $matches;
    }

    public function show($id) {
        $match = DB::select("SELECT 
                rounds.starttime as 'time',
                courts.name as 'court',
                user1a.name as `player1a`,
                user1a.id as `player1a_id`,
                user1a.avatar as `player1a_avatar`,
                user1b.name as `player1b`,
                user1b.id as `player1b_id`,
                user1b.avatar as `player1b_avatar`,
                user2a.name as `player2a`,
                user2a.id as `player2a_id`,
                user2a.avatar as `player2a_avatar`,
                user2b.name as `player2b`,
                user2b.id as `player2b_id`,
                user2b.avatar as `player2b_avatar`,
                matches.id,
                matches.score1,
                matches.score2,
                tournaments.name as `tournament_name`,
                tournaments.id as `tournament_id`
            FROM `schedules`
                INNER JOIN `tournaments` ON schedules.tournament_id = tournaments.id
                INNER JOIN `rounds` ON schedules.round_id = rounds.id
                INNER JOIN `courts` ON schedules.court_id = courts.id
                INNER JOIN `matches` ON schedules.match_id = matches.id
                INNER JOIN `players` as player1a ON matches.player1a_id = player1a.id
                INNER JOIN `players` as player1b ON matches.player1b_id = player1b.id
                INNER JOIN `players` as player2a ON matches.player2a_id = player2a.id
                INNER JOIN `players` as player2b ON matches.player2b_id = player2b.id
                INNER JOIN `users` as user1a ON player1a.user_id = user1a.id
                INNER JOIN `users` as user1b ON player1b.user_id = user1b.id
                INNER JOIN `users` as user2a ON player2a.user_id = user2a.id
                INNER JOIN `users` as user2b ON player2b.user_id = user2b.id
            WHERE schedules.match_id = ". $id);

        $match[0]->time = date('H:i', strtotime($match[0]->time));
        
        return view('match', ['match' => $match[0]]);
    }
    
    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:matches',
            'score1' => 'required',
            'score2' => 'required',
        ]);

        $match = MatchDetails::find($request->get('id'));

        $player1a = Player::find($match->player1a_id);
        $player1b = Player::find($match->player1b_id);
        $player2a = Player::find($match->player2a_id);
        $player2b = Player::find($match->player2b_id);

        if (!Auth::user()->can('admin')) {
            if ($player1a->user_id != Auth::id() && $player1b->user_id != Auth::id() && 
                $player2a->user_id != Auth::id() && $player2b->user_id != Auth::id()) {
                return Response::json("You are not allowed to save the score for this match", 400);
            }
            
            if ($match->score1 != null || $match->score2 != null) {
                return Response::json("Score has been set already for this match", 400);
            }
        }

        if ($request->get('score1') == "" || $request->get('score1') < 0) {
            $match->score1 = 0;
        } elseif ($match->score1 != null) {
            // Update score
            $player1a->points += ($request->get('score1') - $match->score1);
            $player1b->points += ($request->get('score1') - $match->score1);

            $match->score1 = $request->get('score1');
        } else {
            // Add score
            $player1a->points += $request->get('score1');
            $player1b->points += $request->get('score1');

            $match->score1 = $request->get('score1');
        }

        if ($request->get('score2') == "" || $request->get('score2') < 0) {
            $match->score2 = 0;
        } elseif ($match->score2 != null) {
            // Update score
            $player2a->points += ($request->get('score2') - $match->score2);
            $player2b->points += ($request->get('score2') - $match->score2);

            $match->score2 = $request->get('score2');
        } else {
            // Add score
            $player2a->points += $request->get('score2');
            $player2b->points += $request->get('score2');

            $match->score2 = $request->get('score2');
        }

        $match->save();
        $player1a->save();
        $player1b->save();
        $player2a->save();
        $player2b->save();

        return back()->withInput();
    }

    public function store_match($tournament_id, $slot_id) {
        $match = new MatchDetails([
            'tournament_id' => $tournament_id,
            'player1a_id' => 0,
            'player2a_id' => 0,
        ]);

        $match->save();

        Schedule::where('id', $slot_id)->update(['match_id' => $match->id]);
        
        return Redirect::route('edit-match', ['match_id' => $match->id]);
    }

    public function edit_match($id) {
        $match = DB::select("SELECT 
                rounds.starttime as 'time',
                courts.name as 'court',
                user1a.name as `player1a`,
                user1a.id as `player1a_id`,
                user1a.avatar as `player1a_avatar`,
                user1b.name as `player1b`,
                user1b.id as `player1b_id`,
                user1b.avatar as `player1b_avatar`,
                user2a.name as `player2a`,
                user2a.id as `player2a_id`,
                user2a.avatar as `player2a_avatar`,
                user2b.name as `player2b`,
                user2b.id as `player2b_id`,
                user2b.avatar as `player2b_avatar`,
                matches.id,
                matches.score1,
                matches.score2,
                tournaments.name as `tournament_name`,
                tournaments.id as `tournament_id`
            FROM `schedules`
                INNER JOIN `tournaments` ON schedules.tournament_id = tournaments.id
                INNER JOIN `rounds` ON schedules.round_id = rounds.id
                INNER JOIN `courts` ON schedules.court_id = courts.id
                INNER JOIN `matches` ON schedules.match_id = matches.id
                LEFT JOIN `players` as player1a ON matches.player1a_id = player1a.id
                LEFT JOIN `players` as player1b ON matches.player1b_id = player1b.id
                LEFT JOIN `players` as player2a ON matches.player2a_id = player2a.id
                LEFT JOIN `players` as player2b ON matches.player2b_id = player2b.id
                LEFT JOIN `users` as user1a ON player1a.user_id = user1a.id
                LEFT JOIN `users` as user1b ON player1b.user_id = user1b.id
                LEFT JOIN `users` as user2a ON player2a.user_id = user2a.id
                LEFT JOIN `users` as user2b ON player2b.user_id = user2b.id
            WHERE schedules.match_id = ". $id);

        $match[0]->time = date('H:i', strtotime($match[0]->time));

        $tournament_players = Player::where('tournament_id', $match[0]->tournament_id)
            ->select('players.*', 'users.id as user_id', 'users.name')
            ->leftJoin('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();

        return view('admin.match-edit', ['match' => $match[0], 'tournament_players' => $tournament_players]);
    }

    public function update_match(Request $request) {
        $request->validate([
            'id' => 'required|exists:matches',
            'player1a_id' => 'required|exists:players,id',
            'player1b_id' => 'required|exists:players,id',
            'player2a_id' => 'required|exists:players,id',
            'player2b_id' => 'required|exists:players,id',
        ]);

        $match = MatchDetails::find($request->get('id'));
        $match->player1a_id = $request->get('player1a_id');
        $match->player1b_id = $request->get('player1b_id');
        $match->player2a_id = $request->get('player2a_id');
        $match->player2b_id = $request->get('player2b_id');
        $match->save();

        $schedule = Schedule::where('match_id', $request->get('id'))->first();
        return redirect()->to(route('tournament', ['tournament_id' => $schedule->tournament_id]) .'#slot'. $schedule->id)->with('status', 'Manually filled slot');
    }
}
