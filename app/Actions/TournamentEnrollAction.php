<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Tournament;
use Auth;
use MagicLink\Actions\ActionAbstract;

class TournamentEnrollAction extends ActionAbstract
{
    public function __construct(public $email, public $tournament_id)
    {
    }

    public function run()
    {
        if (User::where('email', $this->email)->exists()) {
            $tournament = Tournament::findOrFail($this->tournament_id);
            if (Auth::check()) {
                return view('tournament-enroll', ['tournament' => $tournament]);
            } else {
                if (Auth::login(User::where('email', $this->email)->first())) {
                    return view('tournament-enroll', ['tournament' => $tournament]);
                }
            }
        } else {
            // Show register page with information to redirect user to page to enroll for this tournament afterward
            return view('auth.register', ['tournament_id' => $this->tournament_id]);
        }
    }
}