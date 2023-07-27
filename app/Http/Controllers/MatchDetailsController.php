<?php

namespace App\Http\Controllers;

use App\Models\MatchDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class MatchDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $matches = MatchDetails::all();
        return $matches;
    }

    public function show($id) {
        $match = MatchDetails::findOrFail($id);
        return $match;
    }
}
