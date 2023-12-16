<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\Round;
use App\Models\Schedule;
use App\Models\UserOrganizationalAssignment;
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
        $user = Auth::user();
        $user_tournaments = Player::where('user_id', Auth::id())->get();
        $user_organizations = UserOrganizationalAssignment::where('user_id', Auth::id())
            ->join('organizations', 'organizations.id', '=', 'users_organizational_assignment.organization_id');

        $user_clinics = array();
        if (Player::where('user_id', Auth::id())->where('clinic', 1)->count() > 0) {
            foreach ($user_tournaments as $player) {
                $clinics = Schedule::where('schedules.tournament_id', $player->tournament_id)
                    ->where('schedules.state', 'clinic')
                    ->where('schedules.public', 1)
                    ->join('rounds', 'rounds.id', '=', 'schedules.round_id')
                    ->join('courts', 'courts.id', '=', 'schedules.court_id')
                    ->select(
                        'courts.name as court',
                        'rounds.starttime as time')
                    ->get();
                
                foreach ($clinics as $clinic) {
                    $clinic->time = date('H:i', strtotime($clinic->time));

                    $clinic->players = Player::where('tournament_id', $player->tournament_id)
                        ->where('players.clinic', 1)
                        ->join('users', 'users.id', '=', 'players.user_id')
                        ->select(
                            'users.name as user_name',
                            'users.id as user_id')
                        ->get();

                    $user_clinics[] = $clinic;
                }
            }   
        }

        $user_matches_per_tournament = array();
        foreach ($user_tournaments as $player) {
            $matches = DB::select("SELECT 
                    rounds.starttime as 'time',
                    courts.name as 'court',
                    user1a.name as `player1a`,
                    user1a.id as `player1a_id`,
                    user1a.avatar as `player1a_avatar`,
                    user1a.rating as `player1a_rating`,
                    user1b.name as `player1b`,
                    user1b.id as `player1b_id`,
                    user1b.avatar as `player1b_avatar`,
                    user1b.rating as `player1b_rating`,
                    user2a.name as `player2a`,
                    user2a.id as `player2a_id`,
                    user2a.avatar as `player2a_avatar`,
                    user2a.rating as `player2a_rating`,
                    user2b.name as `player2b`,
                    user2b.id as `player2b_id`,
                    user2b.avatar as `player2b_avatar`,
                    user2b.rating as `player2b_rating`,
                    matches.id,
                    matches.score1,
		    matches.score2,
			tournaments.datetime_end
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
		WHERE schedules.tournament_id = ". $player->tournament_id ." AND 
			tournaments.datetime_end > '". date('Y-m-d H:i:s') ."' AND
                    schedules.public = 1 AND
                    (player1a.id = ". $player->id ." 
                        OR player1b.id = ". $player->id ." 
                        OR player2a.id = ". $player->id ." 
                        OR player2b.id = ". $player->id .")
                ORDER BY time ASC");

            foreach($matches as $match) {
                $match->time = date('H:i', strtotime($match->time));
            }

            $user_matches_per_tournament[] = $matches;
        }

        $your_tournaments = Tournament::leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
            ->select('tournaments.*', 'organizations.name as organizer')
	    ->where('tournaments.datetime_end', '>', date('Y-m-d H:i:s'))
	    ->leftJoin('users_organizational_assignment', 'users_organizational_assignment.organization_id', '=', 'organizations.id')
	    ->where('user_id', Auth::id())
            ->get();

        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        foreach ($your_tournaments as $tournament) {
            $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());

            // Check whether user is enrolled in this tournament or not
            $tournament->is_enrolled = false;
            if (Player::where('tournament_id', $tournament->id)->where('user_id', Auth::id())->count() > 0) {
                $tournament->is_enrolled = true;
            }

            // Prepare number of players in tournament
            $no_players = Player::where('tournament_id', $tournament->id)->count();

            $tournament->can_enroll = true;
            if ((!empty($tournament->enroll_until) && date('Y-m-d H:i') > $tournament->enroll_until) ||
                (!empty($tournament->max_players) && $tournament->max_players != 0 && $no_players >= $tournament->max_players)) {
                $tournament->can_enroll = false;
            }

            // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
            if (!empty($tournament->enroll_until)) {
                $tournament->enroll_until = date('Y-m-d H:i', strtotime($tournament->enroll_until));
            }

            $tournament->score = 0;
            $points = Player::where('user_id', Auth::id())->where('tournament_id', $tournament->id)->select('points')->get();
            foreach ($points as $point) {
                $tournament->score += $point->points;
            }
        }

        return view('home', [
            'first_name' => strtok($user->name, " "),
            'user_clinics' => $user_clinics,
            'user_matches_per_tournament' => $user_matches_per_tournament, 
            'your_tournaments' => $your_tournaments,
        ]);
    }
}
