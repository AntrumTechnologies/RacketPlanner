<?php

namespace App\Http\Controllers;

use App\Models\AdminOrganizationalAssignment;
use App\Models\Tournament;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function show($tournamentId) 
    {
        $tournament = Tournament::findOrFail($tournamentId);
        
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $players = Player::where('tournament_id', $tournamentId)
            ->join('users', 'users.id', '=', 'players.user_id')
            ->select(
                'players.*',
                'users.id as user_id',
                'users.name as user_name')
            ->orderBy('points', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.leaderboard', ['tournament' => $tournament, 'players' => $players]);
    }
}
