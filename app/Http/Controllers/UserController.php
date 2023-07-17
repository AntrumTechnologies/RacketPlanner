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
        $matches = DB::table('matches')->where('tournament', 1)
            ->leftJoin('users as player1', 'player1.id', '=', 'matches.player1')
            ->leftJoin('users as player2', 'player2.id', '=', 'matches.player2')
            ->leftJoin('users as player3',  function ($f) {
                $f->on('player3.id', '=', 'matches.player3')->whereNotNull('player3.id');
            })
            ->leftJoin('users as player4',  function ($f) {
                $f->on('player4.id', '=', 'matches.player4')->whereNotNull('player4.id');
            })
            ->leftJoin('courts', 'courts.id', '=', 'matches.court')
            ->select('matches.id',
                'courts.name as court',
                'matches.datetime',
                'matches.score1_2',
                'matches.score3_4',
                'player1.name as player1',
                'player1.id as player1_id',
                'player2.name as player2',
                'player2.id as player2_id',
                'player3.name as player3',
                'player3.id as player3_id',
                'player4.name as player4',
                'player4.id as player4_id',)
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
