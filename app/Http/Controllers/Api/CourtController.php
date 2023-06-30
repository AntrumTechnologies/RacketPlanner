<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CourtController extends Controller
{
    public function index() {
        $courts = Court::all();
        return Response::json($courts, 200);
    }

    public function show($id) {
        $court = Court::findOrFail($id);
        if ($court) {
            return Response::json($court, 200);
        } else {
            return Response::json("The given court could not be found.", 400);
        }
    }

    /**
     * Hosts the ability to mass create new courts
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
			'name' => 'required|max:30|unique:courts',
            'type' => 'required|max:30',
			'availability_start' => 'required|date_format:Y-m-d H:i',
            'availability_end' => 'required|date_format:Y-m-d H:i',
		]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $newCourt = new Court([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'availability_start' => $request->get('availability_start'),
            'availability_end' => $request->get('availability_end'),
            'created_by' => 0,
        ]);

        $newCourt->save();
        if ($newCourt) {
            return Response::json("Successfully saved new court", 200);
        } else {
            return Response::json("Failed to save new court", 400);
        }
    }

    /**
     * Hosts the ability to update a court one by one
     */
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:courts',
            'name' => 'sometimes|required|max:30',
            'type' => 'sometimes|required|max:30',
            'availability_start' => 'sometimes|required|date_format:yyyy-mm-dd H:i',
            'availability_end' => 'sometimes|required|date_format:yyyy-mm-dd H:i',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $court = Court::find($id);
        
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
            $court->availability_start = $request->get('availability_end');
        }

        $court->save();
        return Response::json("Court has been updated successfully", 200);
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