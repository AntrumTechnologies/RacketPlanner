<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiscController extends PlannerController
{
    private $tournamentId;

    public function __construct($tournamentId)
    {
        $this->middleware('auth');
        $this->tournamentId = $tournamentId;
    }
    
    public function ResetPlanning() {
        Report::Trace(__METHOD__);

        Schedule::where('tournament_id', $this->tournamentId)->where('state', 'available')->update(['match_id' => null]);
    }

    public function GetMatchesForRound($roundId) {
        Report::Trace(__METHOD__);

        return MatchDetails::select('matches.*')
            ->join('schedules', 'matches.id', '=', 'schedules.match_id')
            ->where('matches.tournament_id', $this->tournamentId)
            ->where('schedules.round_id', $this->roundId)
            ->get();
    }

    public function GetPlayerIdsForRound($roundId) {
        Report::Trace(__METHOD__);

        $matches = $this->GetMatchesForRound($roundId);
        $players = array();
        foreach ($matches as $match) {
            $players[] = $match->player1a;
            $players[] = $match->player1b;
            $players[] = $match->player2a;
            $players[] = $match->player2b;
        }

        return array_unique($players);
    }

    public function GetScheduleForPlayer($playerId) {
        Report::Trace(__METHOD__);

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
            WHERE schedules.tournament_id = ". $this->tournamentId ." AND 
                (player1a.id = ". $playerId ." 
                    OR player1b.id = ". $playerId ." 
                    OR player2a.id = ". $playerId ." 
                    OR player2b.id = ". $playerId .")");

        return $matches;
    }
}
