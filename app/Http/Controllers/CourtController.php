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

    public function index() {
        $courts = Court::all();

        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        foreach ($courts as $court) {
            $court->availability_start = date('Y-m-d H:i', strtotime($court->availability_start));
            $court->availability_end = date('Y-m-d H:i', strtotime($court->availability_end));
        }

        return view('courts', ['courts' => $courts]);
    }

    public function show($id) {
        $court = Court::findOrFail($id);
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        $court->availability_start = date('Y-m-d H:i', strtotime($court->availability_start));
        $court->availability_end = date('Y-m-d H:i', strtotime($court->availability_end));

        return view('court', ['court' => $court]);
    }

    /**
     * Hosts the ability to mass create new courts
     */
    public function store(Request $request) {
        $request->validate([
			'name' => 'required|max:30|unique:courts',
            'type' => 'required|max:30',
			'availability_start' => 'required|date_format:Y-m-d H:i',
            'availability_end' => 'required|date_format:Y-m-d H:i',
		]);

        $newCourt = new Court([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'availability_start' => $request->get('availability_start'),
            'availability_end' => $request->get('availability_end'),
            'created_by' => Auth::id(),
        ]);

        $newCourt->save();
        return Redirect::route('courts')->with('status', 'Successfully added the court '. $newCourt->name);
    }

    /**
     * Hosts the ability to update a court one by one
     */
    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:courts',
            'name' => 'sometimes|required|max:30',
            'type' => 'sometimes|required|max:30',
            'availability_start' => 'sometimes|nullable|date_format:Y-m-d H:i',
            'availability_end' => 'sometimes|nullable|date_format:Y-m-d H:i',
        ]);

        $court = Court::find($request->get('id'));
        
        if ($request->has('name')) {
            $court->name = $request->get('name');
        }
        
        if ($request->has('type')) {
            $court->type = $request->get('type');
        }
        
        if ($request->has('availability_start')) {
            $court->availability_start = $request->get('availability_start');
        }

        if ($request->has('availability_end')) {
            $court->availability_end = $request->get('availability_end');
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

        return Response::json("Court has been deleted", 200);
    }
}
