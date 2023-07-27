<?php

namespace App\Http\Controllers;

use App\Models\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class RoundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $rounds = Round::all();
        
        return view('admin.rounds', ['rounds' => $rounds]);
    }

    public function show($id, $tournament_id) {
        $round = Round::where('id', $id)->where('tournament_id', $tournament_id)->first(); // Will be only one results anyway due to ID
        
        return view('admin.round', ['round' => $round]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:30',
            'tournament_id' => 'required|exists:tournaments,id',
            'starttime' => 'required',
            'endtime' => 'required',
        ]);

        $new_round = new Round([
            'name' => $request->get('name'),
            'tournament_id' => $request->get('tournament_id'),
            'starttime' => $request->get('starttime'),
            'endtime' => $request->get('endtime'),
        ]);

        $new_round->save();

        $rounds = Round::all();
        return Redirect::route('admin.rounds', ['rounds' => $rounds]);
    }

    public function delete($id) {
        // TODO(PATBRO): add constraints that user is not part of any matches for example
        $round = Round::findOrFail($id);
        $round->delete();

        return Redirect::route('rounds')->with('status', 'Successfully deleted round '. $round->name);
    }
}
