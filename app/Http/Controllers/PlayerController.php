<?php

namespace App\Http\Controllers;

use App\Actions\TournamentEnrollAction;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use MagicLink\MagicLink;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);
        $tournament_players = Player::where('tournament_id', $tournament_id)
            ->select('players.*', 'users.name', 'users.email')
            ->leftJoin('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();
        // Only return users that are not yet assigned to the given tournament
        $user_ids = Player::where('tournament_id', $tournament_id)->pluck('user_id')->all();
        $users = User::whereNotIn('id', $user_ids)->get();

        return view('admin.players', ['tournament' => $tournament, 'tournament_players' => $tournament_players, 'users' => $users]);
    }

    public function invite(Request $request) {
        $tournament = Tournament::findOrFail($request->get('tournament_id'));

        $enrollAction = new TournamentEnrollAction($request->get('email'), $tournament);
        $magicUrl = MagicLink::create($enrollAction)->url;

        $array = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'url' => $magicUrl,
            'tournament' => $tournament,
        ];

        if (Users::where('email', $request->get('email'))->exists()) {
            $user = Users::where('email', $request->get('email'))->first();
            $user->notify(new TournamentEnrollEmail($array));
        } else {
            Notification::route('mail', $request->get('email'))
                ->notify(new TournamentEnrollEmail($array));
        }
    }

    public function markPresent(Request $request) {
        $request->validate([
            'id' => 'required|exists:players',
        ]);

        $player = Player::findOrFail($request->get('id'));

        $player->present = true;
        $player->save();

        $user = User::findOrFail($player->user_id);
        return Redirect::route('players', ['tournament_id' => $player->tournament_id])->with('status', 'Marked '. $user->name .' present');
    }

    public function markAbsent(Request $request) {
        $request->validate([
            'id' => 'required|exists:players',
        ]);

        $player = Player::findOrFail($request->get('id'));

        $player->present = false;
        $player->save();

        $user = User::findOrFail($player->user_id);
        return Redirect::route('players', ['tournament_id' => $player->tournament_id])->with('status', 'Marked '. $user->name .' absent');
    }

    public function enroll(Request $request) {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'clinic' => 'sometimes',
        ]);

        // TODO: add validation like whether the deadline has not been reached yet

        $new_player = new Player([
            'user_id' => Auth::id(),
            'tournament_id' => $request->get('tournament_id'),
            'clinic' => $request->get('clinic'),
        ]);

        $new_player->save();

        return redirect()->route('tournament', ['tournamnet_id' => $request->get('tournament_id')]);
    }

    public function withdraw(Request $request) {
        // TODO(PATBRO): add constraints that user is not part of any matches for example
        $request->validate([
            'id' => 'required|exists:players',
            'name' => 'required',
        ]);

        $player = Player::find($request->get('id'));
        $player->delete();

        return redirect()->route('tournament', ['tournamnet_id' => $request->get('tournament_id')]);
    }

    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tournament_id' => 'required|exists:tournaments,id',
            'clinic' => 'sometimes',
        ]);

        $new_player = new Player([
            'user_id' => $request->get('user_id'),
            'tournament_id' => $request->get('tournament_id'),
            'clinic' => $request->get('clinic'),
        ]);

        $new_player->save();

        $user = User::find($request->get('user_id'));
        return Redirect::route('players', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully assigned '. $user->name .' to tournament');
    }

    public function delete(Request $request) {
        // TODO(PATBRO): add constraints that user is not part of any matches for example
        $request->validate([
            'id' => 'required|exists:players',
            'name' => 'required',
        ]);

        $player = Player::find($request->get('id'));
        $player->delete();

        return Redirect::route('players', ['tournament_id' => $player->tournament_id])->with('status', 'Successfully deleted player '. $request->get('name'));
    }
}
