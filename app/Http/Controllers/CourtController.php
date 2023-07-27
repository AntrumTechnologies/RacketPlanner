<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CourtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($tournament_id) {
        $courts = Court::where('tournament_id', $tournament_id);

        return view('admin.courts', ['courts' => $courts]);
    }

    public function show($id) {
        $court = Court::findOrFail($id);

        return view('admin.court', ['court' => $court]);
    }

    public function store(Request $request) {
        $request->validate([
			'name' => 'required|max:30',
            'tournament_id' => 'required|exists:tournaments,id',
		]);

        $newCourt = new Court([
            'name' => $request->get('name'),
            'tournament_id' => $request->get('tournament_id'),
            'created_by' => Auth::id(),
        ]);

        $newCourt->save();
        return Redirect::route('courts')->with('status', 'Successfully added the court '. $newCourt->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:courts',
            'name' => 'sometimes|required|max:30',
            'tournament_id' => 'sometimes|required|exists:tournaments,id',
        ]);

        $court = Court::find($request->get('id'));
        
        if ($request->has('name')) {
            $court->name = $request->get('name');
        }

        if ($request->has('tournament_id')) {
            $court->tournament_id = $request->get('tournament_id');
        }

        $court->save();
        
        return Redirect::route('courts')->with('status', 'Successfully updated court details for '. $court->name);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:courts',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $court = Court::find($id);
        $court->delete();

        return Redirect::route('courts')->with('status', 'Successfully deleted court '. $court->name);
    }
}
