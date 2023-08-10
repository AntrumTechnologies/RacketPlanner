<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Player;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function show($tournamentId) 
    {
        $tournament = Tournament::where('id', $tournamentId)->first();

        $players = Player::where('tournament_id', $tournamentId)
            ->join('users', 'users.id', '=', 'players.user_id')
            ->select(
                'players.*',
                'users.id as user_id',
                'users.name as user_name')
            ->orderBy('points', 'desc')
            ->orderBy('name', 'asc')
            ->get();

            return view('admin.leaderboard', ['tournament' => $tournament, 'players' => $players]);
    }
}
