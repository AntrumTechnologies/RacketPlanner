<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentUser;
use App\Models\MatchDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home() {
        $matches = DB::table('matches')->where('tournament', $id)
            ->leftJoin('users as player1a', 'player1a.id', '=', 'matches.player1a')
            ->leftJoin('users as player2a', 'player2a.id', '=', 'matches.player2a')
            ->leftJoin('users as player1b',  function ($f) {
                $f->on('player1b.id', '=', 'matches.player1b')->whereNotNull('player1b.id');
            })
            ->leftJoin('users as player2b',  function ($f) {
                $f->on('player2b.id', '=', 'matches.player2b')->whereNotNull('player2b.id');
            })
            ->leftJoin('courts', 'courts.id', '=', 'matches.court')
            ->select('matches.id',
                'courts.name as court',
                'matches.datetime',
                'matches.score1_2',
                'matches.score3_4',
                'player1a.name as player1a',
                'player1a.id as player1a_id',
                'player2a.name as player2a',
                'player2a.id as player2a_id',
                'player1b.name as player1b',
                'player1b.id as player1b_id',
                'player2b.name as player2b',
                'player2b.id as player2b_id',)
            ->get();
        $matches = $matches->groupBy('datetime');

        $tournaments = Tournament::all();
        
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        foreach ($tournaments as $tournament) {
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
        }

        return view('home', ['matches' => $matches, 'tournaments' => $tournaments]);
    }
    
    public function index() {
        $users = User::all();
        return view('admin.users', ['users' => $users]);
    }

    public function show($id) {
        $user = User::findOrFail($id);

        $tournaments = TournamentUser::where('user', $id)
            ->join('tournaments', 'tournaments.id', '=', 'tournaments_users.tournament')
            ->select('tournaments.id', 'tournaments.name')->get();

        $matches = MatchDetails::where('player1', $id)->orWhere('player2', $id)->orWhere('player3', $id)->orWhere('player4', $id)
            ->join('courts', 'courts.id', '=', 'matches.court')->get();

        return view('admin.user', ['user' => $user, 'tournaments' => $tournaments, 'matches' => $matches]);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:users',
            'name' => 'required',
            'email' => 'required',
            'password' => 'sometimes',
            'rating' => 'min:0',
            'avatar' => 'mimes:jpeg,png|max:4096',
            'availability_start' => 'sometimes|nullable|date_format:Y-m-d H:i',
            'availability_end' => 'sometimes|nullable|date_format:Y-m-d H:i',
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

            $user->avatar = Storage::putFile('avatars', $request->file('avatar'));
        }

        if ($request->has('availability_start')) {
            $user->availability_start = $request->get('availability_start');
        }

        if ($request->has('availability_end')) {
            $user->availability_end = $request->get('availability_end');
        }

        $user->save();

        return Redirect::route('users')->with('status', 'Successfully updated user details for '. $user->name);
    }
}
