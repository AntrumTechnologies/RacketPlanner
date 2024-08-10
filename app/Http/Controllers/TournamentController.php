<?php

namespace App\Http\Controllers;

use App\Actions\TournamentEnrollAction;
use App\Models\Court;
use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Round;
use App\Models\User;
use App\Models\Organization;
use App\Models\AdminOrganizationalAssignment;
use App\Notifications\TournamentEnrollEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use MagicLink\MagicLink;

class TournamentController extends Controller
{
    /**
     * USER ACTIONS
     */
    public static function index() {
        if (Auth::user()->can('superuser')) {
            $tournaments = Tournament::leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
                ->select('tournaments.*', 'organizations.name as organizer', 'organizations.id as organization_id')
                ->orderBy('tournaments.datetime_start', 'DESC')
                ->get();
        } else {
            $tournaments = Tournament::leftJoin('users_organizational_assignment', 'organization_id', '=', 'tournaments.owner_organization_id')
                ->leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
                ->select('tournaments.*', 'organizations.name as organizer', 'organizations.id as organization_id')
                ->orderBy('tournaments.datetime_start', 'DESC')
                ->where('users_organizational_assignment.user_id', Auth::id())->get();
        }

        $is_user_admin = false;

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
            $tournament->can_withdraw = true;
            if (!empty($tournament->enroll_until) && date('Y-m-d H:i') > $tournament->enroll_until) {
                $tournament->can_enroll = false;
                $tournament->can_withdraw = false;
            }
    
            if (!empty($tournament->max_players) && $tournament->max_players != 0 && $no_players >= $tournament->max_players) {
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

            $tournament->is_user_admin = false;
            $isUserAdmin = Tournament::where('tournaments.id', $tournament->id)
                ->leftJoin('admins_organizational_assignment', 'admins_organizational_assignment.id', '=', 'tournaments.owner_organization_id')
                ->where('admins_organizational_assignment.user_id', Auth::id())
                ->first();
            if ($isUserAdmin || Auth::user()->can('superuser')) {
                $tournament->is_user_admin = true;
            }
        }

        if(AdminOrganizationalAssignment::where('user_id', Auth::id())->count() > 0) {
            $is_user_admin = true;
        }

        return view('tournaments', ['tournaments' => $tournaments, 'is_user_admin' => $is_user_admin]);
    }

    public function invite($public_link) {
        $tournament = Tournament::where('tournaments.public_link', $public_link)
            ->leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
            ->select('tournaments.*', 'organizations.name as organizer', 'organizations.id as organization_id')
            ->first();

        return view('tournament-invite', ['tournament' => $tournament]);
    }

    public function show($tournament_id) {
        if (Auth::user()->can('superuser')) {
            $tournament = Tournament::where('tournaments.id', $tournament_id)
                ->leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
                ->select('tournaments.*', 'organizations.name as organizer', 'organizations.id as organization_id')
                ->first();
        } else {
            $tournament = Tournament::where('tournaments.id', $tournament_id)
                ->leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
                // ->leftJoin('users_organizational_assignment', 'users_organizational_assignment.organization_id', '=', 'tournaments.owner_organization_id')
                // ->where('users_organizational_assignment.user_id', Auth::id())
                ->select('tournaments.*', 'organizations.name as organizer', 'organizations.id as organization_id')
                ->first();
        }

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
        $tournament->can_withdraw = true;
        if ((!empty($tournament->enroll_until) && date('Y-m-d H:i') > $tournament->enroll_until) ||
            (date('Y-m-d H:i') > $tournament->start_datetime)) {
            $tournament->can_enroll = false;
            $tournament->can_withdraw = false;
        }

        if (!empty($tournament->max_players) && $tournament->max_players != 0 && $no_players >= $tournament->max_players) {
            $tournament->can_enroll = false;
        }

        $score = 0;
        $points = Player::where('user_id', Auth::id())->where('tournament_id', $tournament_id)->select('points')->get();
        foreach ($points as $point) {
            $score += $point->points;
        }

        // Determine whether courts, rounds, or players changed
        $regenerate_schedule = false;
        $regenerate_matches = false;
        if ($tournament->change_to_courts_rounds == true) {
            // Generate schedule & matches again if courts or rounds change
            $regenerate_schedule = true;
        } else {
            // Generate only matches again if only players change
            if ($tournament->change_to_players == true) {
                $regenerate_matches = true;
            }
        }

        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
        $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
        if ($tournament->enroll_until != null) {
            $tournament->enroll_until = date('d-m-Y H:i', strtotime($tournament->enroll_until));
        }

        $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());

        $players = Player::where('tournament_id', $tournament_id)
            ->select('players.*', 'users.name as name')
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

        $isUserAdmin = Tournament::where('tournaments.id', $tournament_id)
            ->leftJoin('admins_organizational_assignment', 'admins_organizational_assignment.organization_id', '=', 'tournaments.owner_organization_id')
            ->where('admins_organizational_assignment.user_id', Auth::id())
            ->first();
        if ($isUserAdmin || Auth::user()->can('superuser')) {
            $is_user_admin = true;
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
            $is_user_admin = false;
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
                ->orderBy('courts.id', 'asc')
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

        $courts = Court::where('tournament_id', $tournament_id)->count();
        $rounds = Round::where('tournament_id', $tournament_id)->count();
        $matches_scheduled = Schedule::where('tournament_id', $tournament_id)->where('state', 'available')->where('match_id', '!=', NULL)->count();

        return view('tournament', [
            'tournament' => $tournament, 
            'score' => $score,
            'count' => $count, 
            'regenerate_schedule' => $regenerate_schedule,
            'regenerate_matches' => $regenerate_matches,
            'schedule' => $schedule, 
            'schedule_clinic' => $schedule_clinic,
            'next_round_id' => $next_round_id,
            'players' => $players, 
            'courts' => $courts,
            'rounds' => $rounds,
            'matches_scheduled' => $matches_scheduled,
            'is_user_admin' => $is_user_admin,
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

    /**
     * ADMIN ACTIONS
     */
    public function store_invite(Request $request) {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'email' => 'required|email',
        ]);

        $tournament = Tournament::findOrFail($request->get('tournament_id'));
        if (!AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $tournament->owner_organization_id)->count() > 0 &&
            !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $name = '';
        if (User::where('email', $request->get('email'))->exists()) {
            $user = User::where('email', $request->get('email'))->first();
            $name = $user->name;
        }

        $enrollAction = new TournamentEnrollAction($name, $request->get('email'), $request->get('tournament_id'));
        $magicUrl = MagicLink::create($enrollAction, null)->url;

        $array = [
            'name' => $name,
            'email' => $request->get('email'),
            'url' => $magicUrl,
            'tournament_id' => $request->get('tournament_id'),
            'tournament_name' => Tournament::findOrFail($request->get('tournament_id'))->name,
        ];

        if (User::where('email', $request->get('email'))->exists()) {
            $user->notify(new TournamentEnrollEmail($array));
        } else {
            Notification::route('mail', $request->get('email'))
                ->notify(new TournamentEnrollEmail($array));
        }

        return view("auth.verify", ['email' => $request->get('email')]);
    }

    public function edit($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);

        if (!AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $tournament->owner_organization_id)->count() > 0 &&
            !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $organization = Organization::findOrFail($tournament->owner_organization_id);

        return view('admin.tournament-edit', ['tournament' => $tournament, 'organization' => $organization]);
    }

    public function create() {
        $user = Auth::user();
        if ($user->can('superuser')) {
            $organizations = Organization::all();
        } else {
            $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->join('organizations', 'organizations.id', '=', 'admins_organizational_assignment.organization_id')->get();
        }

        if (count($organizations) == 0) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        return view('admin.tournament-create', ['organizations' => $organizations]);
    }

    public function store(Request $request) {
        $request->validate([
            'owner_organization_id' => 'required|exists:organizations,id',
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d\TH:i',
            'datetime_end' => 'required|date_format:Y-m-d\TH:i',
            'description' => 'sometimes',
            'location' => 'sometimes',
            'location_link' => 'sometimes',
            'max_players' => 'required|integer|min:0',
            'enroll_until' => 'sometimes|nullable|date_format:Y-m-d\TH:i',
            'double_matches' => 'required|boolean',
		]);

        if (!AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $request->get('owner_organization_id'))->count() > 0 &&
            !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $newTournament = new Tournament([
            'owner_organization_id' => $request->get('owner_organization_id'),
            'name' => $request->get('name'),
            'datetime_start' => $request->get('datetime_start'),
            'datetime_end' => $request->get('datetime_end'),
            'description' => $request->get('description'),
            'location' => $request->get('location'),
            'location_link' => $request->get('location_link'),
            'max_players' => $request->get('max_players'),
            'enroll_until' => $request->get('enroll_until'),
            'public_link' => Str::random(21),
            'double_matches' => $request->get('double_matches'),
        ]);

        $newTournament->save();
        return Redirect::route('tournaments')->with('status', 'Successfully added the tournament '. $newTournament->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:tournaments',
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d\TH:i',
            'datetime_end' => 'required|date_format:Y-m-d\TH:i',
            'description' => 'sometimes',
            'location' => 'sometimes',
            'location_link' => 'sometimes',
            'max_players' => 'required|integer|min:0',
            'enroll_until' => 'sometimes|nullable|date_format:Y-m-d\TH:i',
            'number_of_matches' => 'required|integer|min:10|max:60',
            'partner_rating_tolerance' => 'required|integer|min:0|max:10',
            'team_rating_tolerance' => 'required|integer|min:0|max:10',
            'double_matches' => 'required|boolean',
            'max_match_count' => 'required|integer|min:1',
		]);

        $tournament = Tournament::find($request->get('id'));

        if (!AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $tournament->owner_organization_id)->count() > 0 &&
            !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        // Log change to players to generate matches again if any of the match generation config parameters change
        if ($tournament->number_of_matches != $request->get('number_of_matches') ||
                $tournament->partner_rating_tolerance != $request->get('partner_rating_tolerance') ||
                $tournament->team_rating_tolerance != $request->get('team_rating_tolerance') ||
                $tournament->double_matches != $request->get('double_matches') ||
                $tournament->max_match_count != $request->get('max_match_count')) {
            $tournament->change_to_players = true;
        }

        $tournament->name = $request->get('name');
        $tournament->datetime_start = $request->get('datetime_start');
        $tournament->datetime_end = $request->get('datetime_end');
        $tournament->description = $request->get('description');
        $tournament->location = $request->get('location');
        $tournament->location_link = $request->get('location_link');
        $tournament->max_players = $request->get('max_players');

        if ($request->has('enroll_until')) {
            $tournament->enroll_until = $request->get('enroll_until');
        } else {
            $tournament->enroll_until = null;
        }

        $tournament->number_of_matches = $request->get('number_of_matches');
        $tournament->partner_rating_tolerance = $request->get('partner_rating_tolerance');
        $tournament->team_rating_tolerance = $request->get('team_rating_tolerance');
        $tournament->double_matches = $request->get('double_matches');
        $tournament->max_match_count = $request->get('max_match_count');

        $tournament->save();
        return Redirect::route('tournament', ['tournament_id' => $tournament->id])->with('status', 'Successfully updated the tournament details');
    }

    public function delete(Request $request) {
        $request->validate([
            'id' => 'required|exists:tournaments',
        ]);

        $tournament = Tournament::find($request->id);

        if (!AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $tournament->owner_organization_id)->count() > 0 &&
            !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $tournament->delete();

        // TODO: delete all corresponding matches as well, and assigned users and courts.

        return Redirect::route('tournaments')->with('status', 'Successfully removed the tournament '. $tournament->name);
    }
}
