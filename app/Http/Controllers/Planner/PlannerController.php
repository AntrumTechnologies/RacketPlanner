<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Planner\MatchDetailsController;
use App\Models\Player;
use App\Models\Schedule;
use Illuminate\Http\Request;

class PlannerController extends Controller
{
    private $min_match_count;

    public function __construct(
        protected MatchDetailsController $match,
        protected ScheduleController $schedule,
    )
    {
        $this->middleware('auth');
    }

    /**
     * First determine the amount of matches to generate
     * Target is 100 times the number of available slots
     * In a schedule of 64 slots for matches, the target for the amount of generated matches is 6400
     * 
     * Initial rules:
     *  - Partners may have a max difference in rating of 2
     *  - Max difference in rating between the teams of a match os 0.5
     *  - A player never plays with the same player
     *  - A player plays agains another player maximum of 2 times
     *  
     * Extra iteration may take place in case of insufficient amount of matches 
     * 
     * Allow a max difference in rating within a team of 3 when insufficient amount of matches.
     * 
     * Allow a max difference in rating within a team of 4 when insufficient amount of matches.
     * 
     * Allow a max difference in rating between teams of 1 when when insufficient amount of matches.
     * 
     * Allow a player to play 2 times with the same player.
     * 
     * Allow max difference in rating between teams of 1 when insufficient amount of matches.
     */

    private function determine_min_match_count() {
        $slots = Schedule::where(['tournament_id', $this->tournament_id, 'state' => 'available'])->get();
        $this->min_match_count = count($slots) * 100;
    }

    protected function generate_teams(&$players, $rating_tolerance) {
        $size = count($players);
        
        for ($i = 0; $i < $size; $i++) {
            for ($j = $i + 1; $j < $size; $j++) {
                $player1 =& $players[$i];
                $player2 =& $players[$j];
                $diff = abs($player1['rating'] - $player2['rating']);

                if ($diff <= abs($rating_tolerance)) {
                    $team = array();
                    $team['players'] = array();
                    $team['players'][] = $player1['id'];
                    $team['players'][] = $player2['id'];
                    $team['rating'] = $player1['rating'] + $player2['rating'];
                    $teams[] = $team;
                }
            }
        }

        return $teams;
    }

    protected function verify_team_rating(&$team1, &$team2, $rating_tolerance) {
        $diff = abs($team1['rating'] - $team2['rating']);
        if ($diff > abs($ratingTolerance)) {
            return false;
        }

        $intersect = array_intersect($team1['players'], $team2['players']);
        if (count($intersect) > 0) {
            return false;
        }

        return true;
    }

    protected function verify_partner_count(&$partner_count, &$team, $max_partner_count) {
        $id0 = $team['players'][0];
        $id1 = $team['players'][1];

        if ($partner_count[$id0][$id1] >= $max_partner_count) {
            return false;
        }

        return true;
    }

    protected function update_partner_count(&$partner_count, &$team) {
        $id0 = $team['players'][0];
        $id1 = $team['players'][1];

        $partner_count[$id0][$id1]++;
        $partner_count[$id1][$id0]++;
    }

    protected function verify_opponent_count(&$opponent_counts, &$team1, &$team2, $max_opponent_count) {
        foreach ($team1['players'] as $player1) {
            foreach ($team2['players'] as $player2) {
                if ($opponent_counts[$player1][$player2] >= $max_opponent_count) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function update_opponent_count(&$opponent_counts, &$team1, &$team2) {
        foreach ($team1['players'] as $player1) {
            foreach ($team2['players'] as $player2) {
                $opponent_counts[$player1][$player2]++;
                $opponent_counts[$player2][$player1]++;
            }
        }

        return true;
    }

    protected function try_generate_matches($partner_rating_tolerance, $team_rating_tolerance, $max_partner_count, $max_opponent_count) {
        $players = Player::where('tournament_id', $this->tournament_id)->get();

        $partner_counts = array();
        $opponent_counts = array();

        foreach ($players as &$player1) {
            $id1 = $player1['id'];
            foreach ($players as &$player2) {
                $id2 = $player2['id'];
                $partner_counts[$id1][$id2] = 0;
                $opponent_counts[$id1][$id2] = 0;
            }
        }

        $teams = $this->generate_teams($players, $partner_rating_tolerance);

        $count = 0;
        foreach ($teams as $team1) {
            foreach ($teams as $team2) {
                $result = $this->verify_team_rating($team1, $team2, $team_rating_tolerance);
                $result &= $this->verify_partner_count($partner_counts, $team1, $max_partner_count);
                $result &= $this->verify_partner_count($partner_counts, $team2, $max_partner_count);
                $result &= $this->verify_opponent_count($opponent_counts, $team1, $team2, $max_opponent_count);

                if ($result == true) {
                    $this->update_partner_count($partner_counts, $team1);
                    $this->update_partner_count($partner_counts, $team2);
                    $this->update_opponent_count($opponent_counts, $team1, $team2);
                    $count++;
                }
            }
        }
    }

    public function generate_matches() {
        $this->determine_min_match_count();
        $this->try_generate_matches(3.0, 0.5, 2, 2);
    }
}
