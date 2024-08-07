<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentUser;
use App\Models\MatchDetails;
use App\Models\Player;
use App\Models\Schedule;
use App\Notifications\GenericPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Image;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() {
        if (Auth::user()->can('superuser')) {
            $users = User::orderBy('name')->get();
            return view('admin.users', ['users' => $users]);
        }

        return redirect('home')->with('error', 'You are not allowed to access this page');
    }

    public function show($id) {
        $user = User::findOrFail($id);

        $user_tournaments = Player::where('user_id', $id)->get();

        $user_clinics = array();
        if (Player::where('user_id', $user->id)->where('clinic', 1)->count() > 0) {
            foreach ($user_tournaments as $player) {
                $isUserAdmin = Tournament::where('tournaments.id', $player->tournament_id)
                    ->leftJoin('admins_organizational_assignment', 'admins_organizational_assignment.id', '=', 'tournaments.owner_organization_id')
                    ->where('admins_organizational_assignment.user_id', Auth::id())
                    ->first();
                
                if ($isUserAdmin || Auth::user()->can('superuser')) {
                    $clinics = Schedule::where('schedules.tournament_id', $player->tournament_id)
                        ->where('schedules.state', 'clinic')
                        ->join('rounds', 'rounds.id', '=', 'schedules.round_id')
                        ->join('courts', 'courts.id', '=', 'schedules.court_id')
                        ->select(
                            'courts.name as court',
                            'rounds.starttime as time')
                        ->get();
                } else {
                    $clinics = Schedule::where('schedules.tournament_id', $player->tournament_id)
                        ->where('schedules.state', 'clinic')
                        ->where('schedules.public', 1)
                        ->join('rounds', 'rounds.id', '=', 'schedules.round_id')
                        ->join('courts', 'courts.id', '=', 'schedules.court_id')
                        ->select(
                            'courts.name as court',
                            'rounds.starttime as time')
                        ->get();
                }
                
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
        $matches = array();
        foreach ($user_tournaments as $player) {
            if (Auth::user()->can('admin')) {
                $matches = DB::select("SELECT 
                        schedules.id as `slot`,
                        rounds.starttime as 'time',
                        tournaments.datetime_start as 'datetime',
                        tournaments.id as `tournament_id`,
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
                        matches.score2
                    FROM `schedules`
                        INNER JOIN `rounds` ON schedules.round_id = rounds.id
                        INNER JOIN `courts` ON schedules.court_id = courts.id
                        INNER JOIN `matches` ON schedules.match_id = matches.id
                        INNER JOIN `tournaments` ON schedules.tournament_id = tournaments.id
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
                            OR player2b.id = ". $player->id .")
                    ");
            } else {
                $matches = DB::select("SELECT 
                        schedules.id as `slot`,
                        rounds.starttime as 'time',
                        tournaments.datetime_start as 'datetime',
                        tournaments.id as `tournament_id`,
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
                        matches.score2
                    FROM `schedules`
                        INNER JOIN `rounds` ON schedules.round_id = rounds.id
                        INNER JOIN `courts` ON schedules.court_id = courts.id
                        INNER JOIN `matches` ON schedules.match_id = matches.id
                        INNER JOIN `tournaments` ON schedules.tournament_id = tournaments.id
                        INNER JOIN `players` as player1a ON matches.player1a_id = player1a.id
                        INNER JOIN `players` as player1b ON matches.player1b_id = player1b.id
                        INNER JOIN `players` as player2a ON matches.player2a_id = player2a.id
                        INNER JOIN `players` as player2b ON matches.player2b_id = player2b.id
                        INNER JOIN `users` as user1a ON player1a.user_id = user1a.id
                        INNER JOIN `users` as user1b ON player1b.user_id = user1b.id
                        INNER JOIN `users` as user2a ON player2a.user_id = user2a.id
                        INNER JOIN `users` as user2b ON player2b.user_id = user2b.id
                    WHERE schedules.tournament_id = ". $player->tournament_id ." AND 
                        schedules.public = 1 AND
                        (player1a.id = ". $player->id ." 
                            OR player1b.id = ". $player->id ." 
                            OR player2a.id = ". $player->id ." 
                            OR player2b.id = ". $player->id .")
                    ");
            }

            foreach($matches as $match) {
                $tournament_date = date('Y-m-d', strtotime($match->datetime));
                $match->datetime = date('Y-m-d H:i', strtotime($tournament_date . ' '. $match->time));
                $match->time = date('d M Y - H:i', strtotime($tournament_date . ' '. $match->time));
            }

            $user_matches_per_tournament = array_merge($user_matches_per_tournament, $matches);
        }

        $keys = array_column($user_matches_per_tournament, 'datetime');
        array_multisort($keys, SORT_DESC, $user_matches_per_tournament);

        return view('user', [
            'user' => $user, 
            'user_tournaments' => $user_tournaments, 
            'user_clinics' => $user_clinics,
            'user_matches_per_tournament' => $user_matches_per_tournament,
            'count_matches' => count($user_clinics) + count($matches),
        ]);
    }

    public function edit($user_id) {
        if ($user_id == Auth::id() || Auth::user()->can('superuser')) {
            $user = User::findOrFail($user_id);
        } else {
            return Redirect::route('user', $user_id);
        }

        return view('user-edit', ['user' => $user]);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:users',
            'name' => 'required',
            'email' => 'nullable|email',
            'password' => 'sometimes',
            'rating' => 'sometimes|min:0',
            'avatar' => 'sometimes|mimes:jpg,jpeg,png|max:4096',
            'fcm_token' => 'sometimes',
        ]);

        $user = User::find($request->get('id'));

        if (Auth::id() != $user->id && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        if ($request->has('name')) {
            $user->name = $request->get('name');
        }

        if ($request->has('email') && !empty($request->has('email'))) {
            $user->email = $request->get('email');
            // TODO: send email address verification email after updating
        }

        if ($request->has('rating')) {
            $user->rating = $request->get('rating');
        }

        if ($request->has('avatar')) {
            if ($user->avatar != null) {
                // Remove old avatar first
                Storage::delete($user->avatar);
            }

            $file = $request->file('avatar');
            $path = $file->hashName('avatars');
            $avatar = Image::make($file)->fit(180)->encode('jpg', 100);
            $store = Storage::disk('public')->put($path, (string) $avatar->encode());

            $user->avatar = $path;
        }

        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->input('fcm_token');
        }
        
        $user->save();

        return Redirect::route('user', $user->id)->with('status', 'Successfully updated user details');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'rating' => 'min:0',
        ]);

        if (!Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $newUser = new User([
            "name" => $request->get('name'),
            "email" => $request->get('email'),
            "rating" => $request->get('rating'),
        ]);

        $newUser->save();
        return Redirect::route('users')->with('status', 'Successfully added user '. $newUser->name);
    }

    public function push_notification()
    {
        $user = Auth::user();
        $user->notify(new GenericPushNotification);
        return Response::json("Push notification sent!", 200);
    }
}
