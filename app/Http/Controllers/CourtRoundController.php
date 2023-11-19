<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Round;
use App\Models\Tournament;
use App\Models\Schedule;
use App\Http\Controllers\PlannerDatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CourtRoundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($tournament_id) {
        $tournament = Tournament::findOrFail($tournament_id);
        $matches_scheduled = Schedule::where('tournament_id', $tournament_id)->where('state', 'available')->where('match_id', '!=', NULL)->count();
        $courts = Court::where('tournament_id', $tournament_id)->get();
        $rounds = Round::where('tournament_id', $tournament_id)->get();

        foreach ($rounds as $round) {
            $round->starttime = date('H:i', strtotime($round->starttime));
            $round->endtime = date('H:i', strtotime($round->endtime));
        }

        return view('admin.courts-rounds', ['tournament' => $tournament, 'matches_scheduled' => $matches_scheduled, 'courts' => $courts, 'rounds' => $rounds]);
    }
}