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

        $user->notify(new MagicEmail($array));

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
            'tournament_id' => 'required',
        ]);

        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            // TODO: add rating
        ]);

        $user->save();

        if (!empty($request->get('tournament_id'))) {
            // TODO: security vulnerability! Verify magic link of invite link before logging in user
            Auth::login($user);

            $tournament = Tournament::findOrFail($request->get('tournament_id'));
            return redirect()->route('enroll-tournament', ['tournament_id' => $tournament->id]);
        } else {
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
