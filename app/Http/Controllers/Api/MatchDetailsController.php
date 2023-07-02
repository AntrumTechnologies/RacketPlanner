<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MatchDetailsController extends Controller
{
    public function show($id) {
        $match = MatchDetails::findOrFail($id);
        if ($match) {
            return Response::json($match, 200);
        } else {
            return Response::json("The given match could not be found.", 400);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:matches',
            'score1_2' => 'required|min:0',
            'score3_4' => 'sometimes|required|min:0',
		]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $match = MatchDetails::find($request->get('id'));
        
        $match->score1_2 = $request->get('score1_2');

        if ($request->has('score3_4')) {
            $match->score3_4 = $request->get('score3_4');
        }

        $match->save();
        return Response::json("Successfully saved match details", 200);
    }
}
