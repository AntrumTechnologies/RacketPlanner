<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Round;
use App\Models\User;
use App\Models\Organization;
use App\Models\AdminOrganizationalAssignment;
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
        $tournaments = Tournament::leftJoin('users_organizational_assignment', 'organization_id', '=', 'owner_organization_id')
            ->leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
            ->select('tournaments.*', 'organizations.name as organizer')
            ->where('users_organizational_assignment.user_id', Auth::id())->get();

        foreach ($tournaments as $tournament) {
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

            $tournament->score = 0;
            $points = Player::where('user_id', Auth::id())->where('tournament_id', $tournament->id)->select('points')->get();
            foreach ($points as $point) {
                $tournament->score += $point->points;
            }

            // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
            if (!empty($tournament->enroll_until)) {
                $tournament->enroll_until = date('Y-m-d H:i', strtotime($tournament->enroll_until));
            }
        }

        return view('tournaments', ['tournaments' => $tournaments]);
    }

    public function show($tournament_id) {
        $tournament = Tournament::where('tournaments.id', $tournament_id)
            ->select('tournaments.*')
            ->join('users_organizational_assignment', 'organization_id', '=', 'owner_organization_id')
            ->where('users_organizational_assignment.user_id', Auth::id())
            ->first();

        if (!$tournament) {
            return "User is not enrolled to the organization this tournament belongs to";
        }

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

        $score = 0;
        $points = Player::where('user_id', Auth::id())->where('tournament_id', $tournament_id)->select('points')->get();
        foreach ($points as $point) {
            $score += $point->points;
        }

        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
        $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));

        $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());

        $players = Player::where('tournament_id', $tournament_id)
            ->select('players.*', 'users.name as name', 'users.rating as rating')
            ->join('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();

        $count = array('present' => 0, 'absent' => 0, 'clinic' => 0); 
        foreach ($players as $player) {
            $matches = DB::select("SELECT * FROM schedules 
                INNER JOIN `matches` ON schedules.match_id = matches.id
                INNER JOIN `players` as player1a ON matches.player1a_id = player1a.id
                INNER JOIN `players` as player1b ON matches.player1b_id = player1b.id
                INNER JOIN `players` as player2a ON matches.player2a_id = player2a.id
                INNER JOIN `players` as player2b ON matches.player2b_id = player2b.id
                WHERE schedules.tournament_id = ". $tournament_id ." AND 
                    (player1a.id = ". $player->id ." 
                        OR player1b.id = ". $player->id ." 
                        OR player2a.id = ". $player->id ." 
                        OR player2b.id = ". $player->id .")");
            
	    if ($player->clinic == true) {
		if ($player->present == true) {
		    $count['present']++;
		    $count['clinic']++;
		}

                $player->no_matches = count($matches) + 1;
            } else {
		if ($player->present == true) {
		    $count['present']++;
		}

                $player->no_matches = count($matches);
	    }

	    if ($player->present == false) {
	        $count['absent']++;
	    }
        }

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
		    'user1a.rating as player1a_rating',
                    'user1b.name as player1b',
                    'user1b.id as player1b_id',
                    'user1b.avatar as player1b_avatar',
		    'user1b.rating as player1b_rating',
                    'user2a.name as player2a',
                    'user2a.id as player2a_id',
                    'user2a.avatar as player2a_avatar',
		    'user2a.rating as player2a_rating',
                    'user2b.name as player2b',
                    'user2b.id as player2b_id',
                    'user2b.avatar as player2b_avatar',
		    'user2b.rating as player2b_rating')
		->orderBy('time', 'asc')
		->orderBy('courts.id', 'asc')
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
		    'user1a.rating as player1a_rating',
                    'user1b.name as player1b',
                    'user1b.id as player1b_id',
                    'user1b.avatar as player1b_avatar',
		    'user1b.rating as player1b_rating',
                    'user2a.name as player2a',
                    'user2a.id as player2a_id',
                    'user2a.avatar as player2a_avatar',
		    'user2a.rating as player2a_rating',
                    'user2b.name as player2b',
                    'user2b.id as player2b_id',
                    'user2b.avatar as player2b_avatar',
		    'user2b.rating as player2b_rating',
            DB::raw('(CASE WHEN user1a.id = '. Auth::id() .' OR user1b.id = '. Auth::id() .' OR user2a.id = '. Auth::id() .' OR user2b.id = '. Auth::id() .' THEN 1 ELSE 0 END) AS user_is_player'))
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
            'score' => $score,
            'count' => $count, 
            'schedule' => $schedule, 
            'schedule_clinic' => $schedule_clinic,
            'next_round_id' => $next_round_id,
            'players' => $players, 
            'courts' => $courts, 
            'rounds' => $rounds,
        ]);
    }

    public function enroll($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);

        return view('tournament-enroll', ['tournament' => $tournament]);
    }

    public function withdraw($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);

        return view('tournament-withdraw', ['tournament' => $tournament]);
    }

    public function edit($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);

        return view('admin.tournament-edit', ['tournament' => $tournament]);
    }

    public function create() {
        $user = Auth::user();
        if ($user->can('superuser')) {
            $organizations = Organization::all();
        } else {
            $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->join('organizations', 'organizations.id', '=', 'admins_organizational_assignment.organization_id')->get();
        }

        return view('admin.tournament-create', ['organizations' => $organizations]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'enroll_until' => 'sometimes|date_format:Y-m-d H:i',
            'max_players' => 'sometimes|min:0',
            'owner_organization_id' => 'required',
		]);

        if (AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $request->get('owner_organization_id'))->count() == 0) {
            // TODO(PATBRO): improve error handling
            return "User is not an administrator of this organization";
        }

        $newTournament = new Tournament([
            'name' => $request->get('name'),
            'datetime_start' => $request->get('datetime_start'),
            'datetime_end' => $request->get('datetime_end'),
            'type' => $request->get('type'),
            'allow_singles' => $request->get('allow_singles'),
            'owner_organization_id' => $request->get('owner_organization_id'),
            'enroll_until' => $request->get('enroll_until'),
            'max_players' => $request->get('max_players'),
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

        if ($request->has('enroll_until')) {
            $tournament->enroll_until = $request->get('enroll_until');
        }

        if ($request->has('max_players')) {
            $tournament->max_players = $request->get('max_players');
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
