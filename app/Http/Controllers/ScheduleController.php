<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Court;
use App\Models\Round;
use App\Models\Match;
use App\Models\Tournament;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($tournament_id) {
        $schedule = Schedule::where('tournament_id', $tournament_id)
            ->leftJoin('tournament', 'tournament.id', '=', 'schedule.tournament_id')
            ->leftJoin('courts', 'courts.id', '=', 'schedule.court_id')
            ->leftJoin('rounds', 'rounds.id', '=', 'schedule.round_id')
            ->leftJoin('matches', 'matches.id', '=', 'schedule.match_id')
            ->leftJoin('players as player1a', 'player1a.id', '=', 'matches.player1a_id')
            ->leftJoin('players as player1b', 'player1b.id', '=', 'matches.player1b_id')
            ->leftJoin('players as player2a', 'player2a.id', '=', 'matches.player2a_id')
            ->leftJoin('players as player2b', 'player2b.id', '=', 'matches.player2b_id')
            ->leftJoin('users as user1a', 'user1a.id', '=', 'player1a.user_id')
            ->leftJoin('users as user1b', 'user1b.id', '=', 'player1b.user_id')
            ->leftJoin('users as user2a', 'user2a.id', '=', 'player2a.user_id')
            ->leftJoin('users as user2b', 'user2b.id', '=', 'player2b.user_id')
            ->get();

        return view('admin.schedule', ['schedule' => $schedule]);
    }
}
