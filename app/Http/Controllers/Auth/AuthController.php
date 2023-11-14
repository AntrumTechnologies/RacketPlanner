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
        $urlToAutoLogin =  MagicLink::create(new LoginAction($user))->url;
        
        $array = [
            'name' => $user->name,
            'email' => $user->email,
            'url' => $urlToAutoLogin,
        ];

        //$user->notify(new MagicEmail($array));
        Auth::login($user);
        redirect('/');

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
            $token = explode(':', $request->get('magiclink_token'));
            $magicLink = MagicLink::where('id', $token[0])->where('token', $token[1])->first();

            if ((!$magicLink) ||
                (!empty($magicLink->max_visits) && $magicLink->num_visits >= $magicLink->max_visits)) {
                return "Link expired";
            }

            // Now save the user, after validating magic link
            $user->save();

            // Set max visits in order to expire the used link
            $magicLink->max_visits = 1;
            $magicLink->save();
            
            Auth::login($user);
            
            $tournament = Tournament::findOrFail($request->get('tournament_id'));
            return redirect()->route('tournament-enroll', ['tournament_id' => $tournament->id]);
        } else {
            // Now save the user
            $user->save();

            $magicUrl =  MagicLink::create(new LoginAction($user))->url;
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
