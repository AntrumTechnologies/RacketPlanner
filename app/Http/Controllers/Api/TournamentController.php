<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\TournamentCourt;
use App\Models\TournamentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{
    public function index() {
        $tournaments = Tournament::all();
        return Response::json($tournaments, 200);
    }

    public function show($id) {
        // ...
    }

    public function showDetails($id) {
        $tournament = Tournament::findOrFail($id);
        
        return Response::json($tournament, 200);
    }

    public function tournamentCourts($id) {
        // ...
    }

    public function tournamentUsers($id) {
        // ...
    }
}
