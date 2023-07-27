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
        $matches = [];
        $tournaments = [];
        //$user = Auth::User();
        //$permission = Permission::create(['name' => 'admin']);
        //$user->givePermissionTo('admin');

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
