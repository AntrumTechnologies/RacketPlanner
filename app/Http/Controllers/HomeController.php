<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $user_tournaments = Player::where('user_id', Auth::id())->get();

        $user_matches_per_tournament = array();
        foreach ($user_tournaments as $player) {
            $matches = DB::select("SELECT 
                    rounds.starttime as 'time',
                    courts.name as 'court',
                    user1a.name as `player1a`,
                    user1a.id as `player1a_id`,
                    user1b.name as `player1b`,
                    user1b.id as `player1b_id`,
                    user2a.name as `player2a`,
                    user2a.id as `player2a_id`,
                    user2b.name as `player2b`,
                    user2b.id as `player2b_id`,
                    matches.id,
                    matches.score1,
                    matches.score2
                FROM `schedules`
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
                WHERE schedules.tournament_id = ". $player->tournament_id ." AND 
                    (player1a.id = ". $player->id ." 
                        OR player1b.id = ". $player->id ." 
                        OR player2a.id = ". $player->id ." 
                        OR player2b.id = ". $player->id .")");

            $user_matches_per_tournament[] = $matches;
        }

        $all_tournaments = Tournament::all();
        
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        foreach ($all_tournaments as $tournament) {
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));

            $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());
        }

        return view('home', ['user_matches_per_tournament' => $user_matches_per_tournament, 'all_tournaments' => $all_tournaments]);
    }
}
