<?php

namespace App\Http\Controllers;

use App\Models\MatchDetails;
use App\Models\Player;
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
}
