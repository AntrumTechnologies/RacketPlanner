<?php

namespace App\Http\Controllers;

use App\Actions\TournamentEnrollAction;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\Schedule;
use App\Models\User;
use App\Models\AdminOrganizationalAssignment;
use App\Models\UserOrganizationalAssignment;
use App\Notifications\TournamentEnrollEmail;
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

    public function show($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);

        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $tournament_players = Player::where('tournament_id', $tournament_id)
            ->select('players.*', 'users.name', 'users.email')
            ->leftJoin('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();
        // Only return users that are not yet assigned to the given tournament
        $user_ids = Player::where('tournament_id', $tournament_id)
            ->pluck('players.user_id')
            ->all();
        $users = User::whereNotIn('users.id', $user_ids)
            ->leftJoin('users_organizational_assignment', 'users_organizational_assignment.user_id', '=', 'users.id')
            ->where('users_organizational_assignment.organization_id', $tournament->owner_organization_id)
            ->select('users.*')
            ->get();


        $count = array('present' => 0, 'absent' => 0, 'clinic' => 0); 
        foreach ($tournament_players as $player) {
            if ($player->clinic == true) {
                if ($player->present == true) {
                    $count['present']++;
                    $count['clinic']++;
                }
            } else {
                if ($player->present == true) {
                    $count['present']++;
                }

                if ($player->present == false) {
                    $count['absent']++;
                }
            }
        }

        $matches_scheduled = Schedule::where('tournament_id', $tournament_id)->where('state', 'available')->where('match_id', '!=', NULL)->count();

        return view('admin.players', ['tournament' => $tournament, 'tournament_players' => $tournament_players, 'users' => $users, 'matches_scheduled' => $matches_scheduled, 'count' => $count]);
    }

    public function invite(Request $request) {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'email' => 'required|email',
            'name' => 'required|min:2',
        ]);

        $tournament = Tournament::findOrFail($request->get('tournament_id'));
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $enrollAction = new TournamentEnrollAction($request->get('name'), $request->get('email'), $request->get('tournament_id'));
        $magicUrl = MagicLink::create($enrollAction, null)->url;

        $array = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'url' => $magicUrl,
            'tournament_id' => $request->get('tournament_id'),
            'tournament_name' => Tournament::findOrFail($request->get('tournament_id'))->name,
        ];

        if (User::where('email', $request->get('email'))->exists()) {
            $user = User::where('email', $request->get('email'))->first();
            $user->notify(new TournamentEnrollEmail($array));
        } else {
            Notification::route('mail', $request->get('email'))
                ->notify(new TournamentEnrollEmail($array));
        }

        return Redirect::route('players', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully invited '. $request->get('name') .' ('. $request->get('email') .')');
    }

    public function markPresent(Request $request) {
        $request->validate([
            'id' => 'required|exists:players',
        ]);

        $player = Player::findOrFail($request->get('id'));

        $player->present = true;
        $player->save();

        $user = User::findOrFail($player->user_id);
        return redirect()->to(route('players', ['tournament_id' => $player->tournament_id]) .'#player'. $player->id)->with('status', 'Marked '. $user->name .' present');
    }

    public function markAbsent(Request $request) {
        $request->validate([
            'id' => 'required|exists:players',
        ]);

        $player = Player::findOrFail($request->get('id'));

        $player->present = false;
        $player->save();

        $user = User::findOrFail($player->user_id);
        return redirect()->to(route('players', ['tournament_id' => $player->tournament_id]) .'#player'. $player->id)->with('status', 'Marked '. $user->name .' absent');
    }

    public function enroll(Request $request) {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            //'clinic' => 'sometimes',
        ]);

        $tournament = Tournament::findOrFail($request->get('tournament_id'));
        if ((strtotime(now()) > strtotime($tournament->enroll_until) && $tournament->enroll_until != null) || 
            (strtotime(now()) > strtotime($tournament->datetime_start))) {
            return back()->with('error', 'You can not enroll anymore. The enrollment deadline has been reached or the tournament already started');
        }

        $no_players = Player::where('tournament_id', $request->get('tournament_id'))->count();
        if ($no_players >= $tournament->max_players && $tournament->max_players > 0) {
            return back()->with('error', 'You can not enroll anymore. The maximum number of players has been reached');
        }

        if(Player::where('user_id', Auth::id())->where('tournament_id', $request->get('tournament_id'))->exists()) {
            return back()->with('error', 'You are already enrolled');
        }

        $new_player = new Player([
            'user_id' => Auth::id(),
            'tournament_id' => $request->get('tournament_id'),
            'rating' => Auth::user()->rating,
            'clinic' => $request->get('clinic'),
        ]);

        $new_player->save();

        // Which organization is the tournament part of?
        $tournament = Tournament::findOrFail($request->get('tournament_id'));

        // Save change made to players
        $tournament->change_to_players = true;
        $tournament->save();

        // Determine whether to add user to this organization or not
        $alreadyPresent = UserOrganizationalAssignment::where('organization_id', $tournament->owner_organization_id)
            ->where('user_id', Auth::id())
            ->get();

        if ($alreadyPresent->count() == 0) {
            $newUserOrganizationalAssignment = new UserOrganizationalAssignment([
                'organization_id' => $tournament->owner_organization_id,
                'user_id' => Auth::id(),
            ]);
    
            $newUserOrganizationalAssignment->save();
        }

        return redirect()->route('tournament', ['tournament_id' => $request->get('tournament_id')]);
    }

    public function withdraw(Request $request) {
        // TODO(PATBRO): add constraints that user is not part of any matches for example
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        $tournament = Tournament::findOrFail($request->get('tournament_id'));
        $player = Player::where('user_id', Auth::id())->where('tournament_id', $request->get('tournament_id'))->get();

        if (count($player) == 0) {
            return back()->with('error', 'You can not withdraw, because you are not enrolled');
        }

        // $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        // if (count($organizations) == 0 && !Auth::user()->can('superuser') && Auth::id() != $player->user_id) {
        //     return redirect('home')->with('error', 'You are not allowed to perform this action');
        // }

        if (($tournament->enroll_until != null && strtotime(now()) > strtotime($tournament->enroll_until)) || 
            (strtotime(now()) > strtotime($tournament->datetime_start))) {
            return back()->with('error', 'You can not withdraw anymore. The withdraw deadline has been reached or the tournament already started');
        }

        $player->each->delete();

        // Save change made to players
        $tournament->change_to_players = true;
        $tournament->save();

        return redirect()->route('tournament', ['tournament_id' => $request->get('tournament_id')]);
    }

    public function manual_add(Request $request) {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'name' => 'required|min:2',
            'email' => 'nullable|email',
            'rating' => 'required|integer|min:0|max:10',
        ]);

        $tournament = Tournament::findOrFail($request->get('tournament_id'));
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        if (!empty($request->get('email')) && User::where('email', $request->get('email'))->first()) {
            $user = User::where('email', $request->get('email'))->first();
        } else {
            $user = new User([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'rating' => $request->get('rating'),
            ]);

            $user->save();
        }

        $new_player = new Player([
            'user_id' => $user->id,
            'tournament_id' => $request->get('tournament_id'),
            'rating' => $request->get('rating'),
            'clinic' => $request->get('clinic'),
        ]);

        $new_player->save();

        // Determine whether to add user to this organization or not
        $alreadyPresent = UserOrganizationalAssignment::where('organization_id', $tournament->owner_organization_id)
            ->where('user_id', $user->id)
            ->get();

        if ($alreadyPresent->count() == 0) {
            $newUserOrganizationalAssignment = new UserOrganizationalAssignment([
                'organization_id' => $tournament->owner_organization_id,
                'user_id' => $user->id,
            ]);
    
            $newUserOrganizationalAssignment->save();
        }

        // Save change made to players
        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_players = true;
        $tournament->save();

        return Redirect::route('players', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully assigned '. $user->name .' to tournament');
    }

    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tournament_id' => 'required|exists:tournaments,id',
            'rating' => 'sometimes',
            'clinic' => 'sometimes',
        ]);

        $tournament = Tournament::findOrFail($request->get('tournament_id'));

        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        if (!$request->has('rating') || empty($request->get('rating'))) {
            $user = User::find($request->get('user_id'));
            $rating = $user->rating;
        } else {
            $rating = $request->get('rating');
        }

        $new_player = new Player([
            'user_id' => $request->get('user_id'),
            'tournament_id' => $request->get('tournament_id'),
            'rating' => $rating,
            'clinic' => $request->get('clinic'),
        ]);

        $new_player->save();

        // Save change made to players
        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_players = true;
        $tournament->save();

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
        $tournament = Tournament::findOrFail($player->tournament_id);

        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $player->delete();

        // Save change made to players
        $tournament->change_to_players = true;
        $tournament->save();

        // Delete user if email address is empty (one-time user)
        $user = User::find($player->user_id);
        if (empty($user->email)) {
            $user->delete();
        }

        return Redirect::route('players', ['tournament_id' => $player->tournament_id])->with('status', 'Successfully deleted player '. $request->get('name'));
    }
}
