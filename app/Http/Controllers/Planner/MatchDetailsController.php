<?php

namespace App\Http\Controllers\Planner;

use App\Models\MatchDetails;

class MatchDetailsController extends PlannerController
{
    public function show($id) {
        $match = MatchDetails::findOrFail($id);
        return $match;
    }

    public function store($tournament_id, $player1a_id, $player1b_id, $player2a_id, $player2b_id, $rating) {
        $newMatchDetails = new MatchDetails([
            'tournament_id' => $tournament_id,
            'player1a_id' => $player1a_id,
            'player1b_id' => $player1b_id,
            'player2a_id' => $player2a_id,
            'player2b_id' => $player2b_id,
            'rating' => $rating,
            'created_by' => Auth::id(),
        ]);

        $newMatchDetails->save();
    }

    public function enable_all_matches($tournament_id) {
        MatchDetails::where('tournament_id', $tournament_id)->update(['disabled' => 0]);
    }

    public function disable_matches_for_player($tournament_id, $player_id) {
        MatchDetails::where(['tournament_id', '=', $tournament_id], ['player1a_id', '=', $player_id])
            ->orWhere(['tournament_id', '=', $tournament_id], ['player1b_id', '=', $player_id])
            ->orWhere(['tournament_id', '=', $tournament_id], ['player2a_id', '=', $player_id])
            ->orWhere(['tournament_id', '=', $tournament_id], ['player2b_id', '=', $player_id])
            ->update(['disabled' => 1]);
    }

    public function get_available_matches($tournament_id) {
        MatchDetails::where('tournament_id', $tournament_id)->where('disabled', 0)->get();
    }
}
