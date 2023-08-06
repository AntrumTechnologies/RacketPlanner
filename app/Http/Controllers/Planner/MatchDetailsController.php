<?php

namespace App\Http\Controllers\Planner;

use App\Models\MatchDetails;

class MatchDetailsController extends PlannerController
{
    private $tournamentId;

    public function __construct($tournamentId)
    {
        $this->middleware('auth');
        $this->tournamentId = $tournamentId;
    }

    public function GetMatches() {
        Report::Trace(__METHOD__);

        return MatchDetails::where('tournament_id', $tournamentId)->get();
    }

    public function GetMatchById($matchId) {
        Report::Trace(__METHOD__);

        return MatchDetails::where('id', $matchId)->where('tournament_id', $tournamentId);
    }

    public function InsertMatch($player1aId, $player1bId, $player2aId, $player2bId, $rating, $ratingDiff) {
        Report::Trace(__METHOD__);

        $newMatch = new MatchDetails([
            "tournament_id" => $this->tournamentId,
            "player1a_id" => $player1aId,
            "player1b_id" => $player1bId,
            "player2a_id" => $player2aId,
            "player2b_id" => $player2bId,
            "rating" => $rating,
            "rating_diff" => $ratingDiff,
        ]);

        return $newMatch->save();
    }

    public function EnableAllMatches() {
        Report::Trace(__METHOD__);

        MatchDetails::where('tournament_id', $tournament_id)->update(['disabled' => 0]);
    }

    public function DisableMatchById($matchId) {
        Report::Trace(__METHOD__);

        return MatchDetails::where(['tournament_id', '=', $this->tournamentId], ['id', '=', $matchId])->update(['disabled' => 1]);
    }

    public function DisableMatchesForPlayer($playerId) {
        Report::Trace(__METHOD__);

        return MatchDetails::where(['tournament_id', '=', $this->tournamentId], ['player1a_id', '=', $playerId])
            ->orWhere(['tournament_id', '=', $this->tournamentId], ['player1b_id', '=', $playerId])
            ->orWhere(['tournament_id', '=', $this->tournamentId], ['player2a_id', '=', $playerId])
            ->orWhere(['tournament_id', '=', $this->tournamentId], ['player2b_id', '=', $playerId])
            ->update(['disabled' => 1]);
    }

    public function GetAvailableMatches() {
        Report::Trace(__METHOD__);

        return MatchDetails::where('tournament_id', $this->tournamentId)->where('disabled', 0)->get();
    }

    public function SetPriorityForMatch($matchId, $priority) {
        Report::Trace(__METHOD__);

        return MatchDetails::where('id', $matchId)->update(['priority' => $priority]);;
    }

    public function GetBestMatch() {
        Report::Trace(__METHOD__);

        $matches = MatchDetails::where('tournament_id', $this->tournamentId)
            ->where('disabled', '0')
            ->orderByAsc('priority')
            ->orderByDesc('rating')
            ->limit(1)
            ->get();

        if ($matches->count() > 0)
            return $matches->first();

        return null;
    }
}
