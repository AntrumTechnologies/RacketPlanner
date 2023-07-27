<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);
        $tournament_players = Player::where('tournament_id', $tournament_id)->leftJoin('users', 'users.id', '=', 'players.user_id')->get();
        // Only return users that are not yet assigned to the given tournament
        $user_ids = Player::where('tournament_id', $tournament_id)->pluck('user_id')->all();
        $users = User::whereNotIn('id', $user_ids)->get();

        return view('admin.players', ['tournament' => $tournament, 'tournament_players' => $tournament_players, 'users' => $users]);
    }

    public function show($id) {
        $player = Player::findOrFail($id);
    
        return view('admin.player', ['player' => $player]);
    }

    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        $new_player = new Player([
            'user_id' => $request->get('user_id'),
            'tournament_id' => $request->get('tournament_id'),
        ]);

        $user = User::find($id);

        $new_player->save();

        $tournament_players = Player::where('tournament_id', $tournament_id)->leftJoin('users', 'users.id', '=', 'players.user_id')->get();
        return Redirect::route('admin.players', ['tournament_players' => $tournament_players])->with('status', 'Successfully assigned '. $user->name .' to tournament');
    }

    public function delete($id) {
        // TODO(PATBRO): add constraints that user is not part of any matches for example
        $player = Player::findOrFail($id);
        $player->delete();

        return Redirect::route('players')->with('status', 'Successfully deleted player '. $player->user_id);
    }
}
