<?php

namespace App\Http\Controllers\Planner;

use App\Model\Report;
use Illuminate\Http\Request;

class DatabaseController extends PlannerController
{
    private $tournamentId;

    public function __construct()
    {
        $this->middleware('auth');
        $this->tournamentId = $tournamentId;
    }

    /**
     * Tournament
     */
    public function UseTournament($id) {
        Report::Trace(__METHOD__);
        $tournament = Tournament::findOrFail($id);
        if (!$tournament) {
            Report::Fail("Unable to use tournament with id: $id");
            return false;
        }

        Report::Info("Using tournament with id: $id");
        $this->tournamentId = $id;
        return true;
    }

    public function GetTournaments() {
        Report::Trace(__METHOD__);

        return Tournament::all();
    }

    public function GetTournamentById($id) {
        Report::Trace(__METHOD__);
    
        return Tournament::findOrFail($id);
    }

    public function GetTournamentByName($name) {
        Report::Trace(__METHOD__);

        return Tournament::where('name', $name)->get();
    }

    public function InsertTournament($name) {
        Report::Trace(__METHOD__);

        $newTournament = new Tournament([
            "name" => $name,
            "created_by" => Auth::id(),
        ]);

        return $newTournament->save();
    }

    public function DeleteTournamentById($id) {
        Report::Trace(__METHOD__);

        $tournament = Tournament::findOrFail($id);
        if (!$tournament) {
            Report::Fail("Tournament '$id' does not exist.");
            return false;
        }

        // TODO(PATBRO): make sure all dependencies (e.g. matches) are deleted as well
        if (!$tournament->delete()) {
            Report::Fail("Tournament '$id' could not be deleted.");
            return false;
        }

        return true;
    }

    /**
     * Users
     */
    public function GetUsers() {
        Report::Trace(__METHOD__);

        return User::all();
    }

    public function GetUserById($id) {
        Report::Trace(__METHOD__);

        return User::findOrFail($id);
    }

    public function GetUserByName($name) {
        Report::Trace(__METHOD__);

        return User::where('name', $name);
    }

    public function InsertUser($name, $email, $rating) {
        Report::Debug(__METHOD__);

        $newUser = new User([
            "name" => $name,
            "email" => $email,
            "rating" => $rating,
        ]);
        
        return $newUser->save();
    }

    public function DeleteUserById($id) {
        Report::Trace(__METHOD__);

        $user = User::findOrFail($id);
        if (!$user) {
            Report::Fail("User '$id' does not exist.");
            return false;
        }

        return $user->delete();
    }

    /**
     * Courts
     */
    public function GetCourts() {
        return Court::where('tournament_id', $tournament_id)->get();
    }

    public function GetCourtById($id) {
        Report::Trace(__METHOD__);

        return Court::where('id', $id)->where('tournament_id', $this->tournamentId); // Do not allow getting courts from other tournaments
    }

    public function GetCourtByName($name) {
        Report::Trace(__METHOD__);

        return Court::where('name', $name)->where('tournament_id', $this->tournamentId);
    }

    public function InsertCourt($name) {
        Report::Trace(__METHOD__);

        $court = new Court([
            "name" => $name,
            "tournament_id" => $this->tournamentId,
            "created_by" => Auth::id(),
        ]);

        return $court->save();
    }

    public function DeleteCourtById($id) {
        Report::Trace(__METHOD__);

        $court = Court::findOrFail($id);
        if (!$court) {
            Report::Fail("Court with id '$id' does not exist.");
            return false;
        }

        return $court->delete();
    }

    /**
     * Rounds
     */
    public function GetRounds() {
        return Round::where('tournament_id', $this->tournamentId);
    }

    public function GetRoundById($id) {
        Report::Trace(__METHOD__);

        return Round::where('id', $id)->where('tournament_id', $this->tournamentId); // Do not allow getting courts from other tournaments
    }

    public function InsertRound() {
        Report::Trace(__METHOD__);

        $newRound = new Round([
            "name" => "Round",
            "tournament_id" => $this->tournamentId,
        ]);

        return $newRound->save();
    }

    public function DeleteRoundById($id) {
        Report::Trace(__METHOD__);

        $round = Round::findOrFail($id);
        if (!$round) {
            Report::Fail("Round with id '$id' does not exist.");
            return false;
        }

        return $round->delete();
    }

    /**
     * Players
     */
    public function GetPlayers() {
        Report::Trace(__METHOD__);

        $query = "SELECT players.id, players.user_id, users.rating FROM `players`INNER JOIN `users` WHERE players.tournament_id = $this->tournamentId AND players.user_id = users.id";
        return DB::select($query);
    }

    public function InsertPlayer($userId) {
        Report::Trace(__METHOD__);

        $newPlayer = new Player([
            "user_id" => $user_id,
            "tournament_id" => $this->tournamentId,
        ]);

        return $newPlayer->save();
    }

    public function DeletePlayerByUserId($userId) {
        Report::Trace(__METHOD__);

        $player = Player::where('user_id', $userId)->where('tournament_id', $tournamentId);
        return $player->delete();
    }

    /**
     * Matches
     */
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

    /**
     * Schedule
     */
    public function GetSchedule() {
        Report::Trace(__METHOD__);

        return Schedule::where('tournament_id', $this->tournamentId);;
    }

    public function DeleteSchedule() {
        Report::Trace(__METHOD__);

        $schedule = Schedule::where('tournament_id', $this->tournamentId);
        $schedule->delete();
    }

    public function GenerateSchedule() {
        Report::Trace(__METHOD__);

        $rounds = Round::where('tournament_id', $this->tournamentId)->get();
        $courts = Court::where('tournament_id', $this->tournamentId)->get();

        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $newSlot = new Schedule([
                    "tournament_id" => $this->tournamentId,
                    "court_id" => $court->id,
                    "round_id" => $round->id,
                ]);

                if (!$newSlot->save()) {
                    Report::Fail("Unable to add slot ('$court->id', '$round->id') to schedule.");
                    return false;
                }
            }
        }

        return true;
    }

    public function SetSlotStateToClinic($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'clinic',
                'match_id' => null,
            ]);
    }

    public function SetSlotStateToDisabled($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'disabled',
                'match_id' => null,
            ]);
    }

    public function SetSlotStateToAvailable($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'available',
                'match_id' => null,
            ]);
    }

    public function AssignMatchToSlot($slotId, $matchId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'available',
                'match_id' => $matchId,
            ]);
    }

    public function DeleteMatchFromSlot($slotId) {
        Report::Trace(__METHOD__);

        return Schedule::where('id', $slotId)
            ->update([
                'state' => 'available',
                'match_id' => null,
            ]);
    }

    public function GetSlotsByRoundId($roundId) {
        Report::Trace(__METHOD__);

        return Schedule::where('tournament_id', $this->tournamentId)->where('round_id', $roundId)->get();
    }

    /**
     * Miscellaneous
     */
    public function ResetPlanning() {
        Report::Trace(__METHOD__);

        Schedule::where('tournament_id', $this->tournamentId)->where('state', 'available')->update(['match_id' => null]);
    }

    public function GetMatchesForRound($roundId) {
        Report::Trace(__METHOD__);

        return MatchDetails::select('matches.*')
            ->join('schedules', 'matches.id', '=', 'schedules.match_id')
            ->where('matches.tournament_id', $this->tournamentId)
            ->where('schedules.round_id', $this->roundId)
            ->get();
    }

    public function GetPlayerIdsForRound($roundId) {
        Report::Trace(__METHOD__);

        $matches = $this->GetMatchesForRound($roundId);
        $players = array();
        foreach ($matches as $match) {
            $players[] = $match->player1a;
            $players[] = $match->player1b;
            $players[] = $match->player2a;
            $players[] = $match->player2b;
        }

        return array_unique($players);
    }

    public function GetScheduleForPlayer($playerId) {
        Report::Trace(__METHOD__);

        $matches = DB::select("SELECT 
                rounds.starttime as 'time',
                courts.name as 'court',
                user1a.name as `player1a`,
                user1a.id as `player1a_id`,
                user1b.name as `player1b`,
                user1b.id as `player1b_id`,
                user2a.name as `player2a`,
                user2a.id as `player2a_id`,
                user2b.name as `player2b`,
                user2b.id as `player2b_id`,
                matches.id,
                matches.score1,
                matches.score2
            FROM `schedules`
                INNER JOIN `rounds` ON schedules.round_id = rounds.id
                INNER JOIN `courts` ON schedules.court_id = courts.id
                INNER JOIN `matches` ON schedules.match_id = matches.id
                INNER JOIN `players` as player1a ON matches.player1a_id = player1a.id
                INNER JOIN `players` as player1b ON matches.player1b_id = player1b.id
                INNER JOIN `players` as player2a ON matches.player2a_id = player2a.id
                INNER JOIN `players` as player2b ON matches.player2b_id = player2b.id
                INNER JOIN `users` as user1a ON player1a.user_id = user1a.id
                INNER JOIN `users` as user1b ON player1b.user_id = user1b.id
                INNER JOIN `users` as user2a ON player2a.user_id = user2a.id
                INNER JOIN `users` as user2b ON player2b.user_id = user2b.id
            WHERE schedules.tournament_id = ". $this->tournamentId ." AND 
                (player1a.id = ". $playerId ." 
                    OR player1b.id = ". $playerId ." 
                    OR player2a.id = ". $playerId ." 
                    OR player2b.id = ". $playerId .")");

        return $matches;
    }
}
