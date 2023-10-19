<?php

namespace App\Actions;

use MagicLink\Actions\ActionAbstract;
use Auth;

class TournamentEnrollAction extends ActionAbstract
{
    public function __construct(public $email, public $tournament)
    {
    }

    public function run()
    {
        if (User::where('email', $this->email)->exists()) {
            if (Auth::check()) {
                return view('tournament.enroll', ['tournament' => $this->tournament]);
            } else {
                if (Auth::login(User::where('email', $this->email)->first())) {
                    return view('tournament.enroll', ['tournament' => $this->tournament]);
                }
            }
        } else {
            // Show register page with information to redirect user to page to enroll for this tournament afterward
            return view('register', ['tournament' => $this->tournament]);
        }
    }
}