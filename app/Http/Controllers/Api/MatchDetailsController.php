<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MatchDetailsController extends Controller
{
    public function scheduler() 
    {
        // TODO
    }

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

    public function store(Request $request)
    {
        /* TODO: prevent match from being added when player is already scheduled for the given time */
        $validator = Validator::make($request->all(), [
            'tournament' => 'required|exists:tournaments,id',
            'player1' => 'required|exists:users,id',
            'player2' => 'required|exists:users,id|different:player1',
            'player3' => 'sometimes|required|exists:users,id|different:player2',
            'player4' => 'sometimes|required|exists:users,id|different:player3',
            'court' => 'required|exists:courts,id',
            'datetime' => 'required|date_format:Y-m-d H:i',
		]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}
        
        $newMatch = new MatchDetails([
            'tournament' =>$request->get('tournament'),
            'player1' => $request->get('player1'),
            'player2' => $request->get('player2'),
            'player3' => $request->get('player3'),
            'player4' => $request->get('player4'),
            'court' => $request->get('court'),
            'datetime' => $request->get('datetime'),
        ]);

        $newMatch->save();
        return Response::json("Successfully saved new match", 200);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:matches',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $match = MatchDetails::find($id);
        $match->delete();

        return Response::json("Match has been deleted", 200);
    }
}
