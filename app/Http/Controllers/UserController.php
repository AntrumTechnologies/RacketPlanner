<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentUser;
use App\Models\MatchDetails;
use App\Models\Player;
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
        $users = User::orderBy('name')->get();
        return view('admin.users', ['users' => $users]);
    }

    public function show($id) {
        $user = User::findOrFail($id);

        $tournaments = Player::where('user_id', $id)->join('tournaments', 'tournaments.id', '=', 'players.tournament_id')->get();

        // TODO(PATBRO): add functionality to calculate total number of points per tournament for a user

        $matches = DB::select("SELECT 
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
                WHERE (player1a.user_id = ". $id ." 
                    OR player1b.user_id = ". $id ." 
                    OR player2a.user_id = ". $id ." 
                    OR player2b.user_id = ". $id .")");

        foreach ($matches as $match) {
            $match->time = date('H:i', strtotime($match->time));
        }   

        return view('user', ['user' => $user, 'tournaments' => $tournaments, 'matches' => $matches]);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:users',
            'name' => 'required',
            'email' => 'required',
            'password' => 'sometimes',
            'rating' => 'sometimes|min:0',
            'avatar' => 'sometimes|mimes:jpg,jpeg,png|max:4096',
        ]);

        $user = User::find($request->get('id'));

        if ($request->has('name')) {
            $user->name = $request->get('name');
        }

        if ($request->has('email')) {
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
            $avatar = Image::make($file)->crop(190, 190, 0, 15)->encode('jpg', 100);
            Storage::disk('public')->put($path, (string) $avatar->encode());

            $user->avatar = $path;
        }
        
        $user->save();

        return Redirect::route('user', [$user->id])->with('status', 'Successfully updated user details');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'rating' => 'min:0',
        ]);

        $newUser = new User([
            "name" => $request->get('name'),
            "email" => $request->get('email'),
            "rating" => $request->get('rating'),
        ]);

        $newUser->save();
        return Redirect::route('users')->with('status', 'Successfully added user '. $newUser->name);
    }
}
