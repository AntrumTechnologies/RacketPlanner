<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Round;
use App\Models\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $schedules = Schedule::all();
        return $schedules;
    }

    public function show($tournament_id) {
        $schedule = Schedule::where('tournament_id', $tournament_id)->get();
        return $schedule;
    }
}
