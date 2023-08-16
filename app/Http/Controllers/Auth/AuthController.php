<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
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
}
