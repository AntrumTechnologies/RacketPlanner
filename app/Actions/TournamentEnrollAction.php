<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Tournament;
use Auth;
use MagicLink\Actions\ActionAbstract;

class TournamentEnrollAction extends ActionAbstract
{
    public function __construct(public $name, public $email, public $tournament_id)
    {
    }

    public function run()
    {
        if (User::where('email', $this->email)->exists()) {
            $tournament = Tournament::findOrFail($this->tournament_id);
            
            $user = User::where('email', $this->email)->first();
            Auth::login($user);
            
            return view('tournament-enroll', ['tournament' => $tournament]);
        } else {
            // Show register page with information to redirect user to page to enroll for this tournament afterward
            return view('auth.register', ['tournament_id' => $this->tournament_id, 'name' => $this->name, 'email' => $this->email]);
        }
    }
}