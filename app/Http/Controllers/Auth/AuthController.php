<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tournament;
use App\Notifications\MagicEmail;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use MagicLink\Actions\LoginAction;
use MagicLink\MagicLink;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|exists:users',
        ]);

        $user = User::where('email', $request->get('email'))->first();

        $loginAction = new LoginAction($user);
        $loginAction->remember();

        $urlToAutoLogin =  MagicLink::create($loginAction, null)->url;
        
        $array = [
            'name' => $user->name,
            'email' => $user->email,
            'url' => $urlToAutoLogin,
        ];

        //$user->notify(new MagicEmail($array));
        Auth::login($user);
        return redirect('/');

        return view("auth.verify", ['email' => $user->email]);
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'rating' => 'required|integer|min:2|max:10',
            'tournament_id' => 'sometimes',
            'magiclink_token' => 'sometimes',
        ]);

        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'rating' => $request->get('rating'),
        ]);

        if (!empty($request->get('tournament_id')) && !empty($request->get('magiclink_token'))) {
            // If the user clicked the invite link, check whether the magic link is valid to prevent logging in as someone else
            $token = explode(':', $request->get('magiclink_token'));
            $magicLink = MagicLink::where('id', $token[0])->where('token', $token[1])->first();

            if ((!$magicLink) ||
                (!empty($magicLink->max_visits) && $magicLink->num_visits >= $magicLink->max_visits)) {
                return back()->withInput()->with('error', 'The invite link used is not valid anymore. Please contact the tournament organizer.');
            }

            // Do not use user input email address, could be altered prior to sending request, use email saved in magic link action
            $user->email = $magicLink->action->email;

            $tournament = Tournament::findOrFail($magicLink->action->tournament_id);
            if (empty($tournament->public_link)) {
                return back()->withInput()->with('error', 'Registration via public invite link is not permitted. Please contact the tournament organizer.');
            }

            // Now save the user, after validating magic link and tournament
            $user->save();

            // Set max visits in order to expire the used link
            if(!empty($request->get('magiclink_token'))) {
                $magicLink->max_visits = 1;
                $magicLink->save();
            }
            
            Auth::login($user);
            
            return redirect()->route('tournament-enroll', ['tournament_id' => $tournament->id]);
        } else {
            // Now save the user
            $user->save();

            $loginAction = new LoginAction($user);
            $loginAction->rememeber();

            $magicUrl =  MagicLink::create($loginAction, null)->url;
            $array = [
                'name' => $user->name,
                'email' => $user->email,
                'url' => $magicUrl,
            ];

            $user->notify(new MagicEmail($array));
            return view("auth.verify", ['email' => $user->email]);
        }
    }
}
