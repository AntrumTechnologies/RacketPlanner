<?php

namespace App\Http\Controllers;

use App\Model\Report;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * YET TO REWRTIE COMPLETE FUNCTION IMPLEMENTATION
     */

    /**
     * Tournament
     */
    public function UseTournament($id) {
        Report::Trace(__METHOD__);
        if ($this->GetTournamentById($id) == null) {
            Report::Fail("Unable to use tournament with id: $id");
            return false;
        }

        Report::Info("Using tournament with id: $id");
        $this->tournamentId = $id;
        return true;
    }

    public function GetTournaments() {
        Report::Trace(__METHOD__);

        return $this->db->Select(TABLE_TOURNAMENTS);
    }

    public function GetTournamentById($id) {
        Report::Trace(__METHOD__);
    
        $search['id'] = $id;
        $results = $this->db->Select(TABLE_TOURNAMENTS, $search);
        return $results[0];
    }

    public function GetTournamentByName($name) {
        Report::Trace(__METHOD__);

        $search['name'] = $name;
        $results = $this->db->Select(TABLE_TOURNAMENTS, $search);
        return $results[0];
    }

    public function InsertTournament($name) {
        Report::Trace(__METHOD__);

        $record['name'] = $name;

        return $this->db->Insert(TABLE_TOURNAMENTS, $record);
    }

    public function DeleteTournamentById($id) {
        Report::Trace(__METHOD__);

        if ($this->GetTournamentById($id) == null) {
            Report::Fail("Tournament '$id' does not exist.");
            return false;
        }

        $record['id'] = $id;

        if ($this->db->Delete(TABLE_TOURNAMENTS, $record) == false) {
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

        return $this->db->Select(TABLE_USERS);
    }

    public function GetUserById($id) {
        Report::Trace(__METHOD__);

        $search['id'] = $id;
        $results = $this->db->Select(TABLE_USERS, $search);
        return $results[0];
    }

    public function GetUserByName($name) {
        Report::Trace(__METHOD__);

        $search['name'] = $name;
        $results = $this->db->Select(TABLE_USERS, $search);
        return $results[0];
    }

    public function InsertUser($name, $email, $rating) {
        Report::Debug(__METHOD__);

        $record['name'] = $name;
        $record['email'] = $email;
        $record['rating'] = $rating;
        return $this->db->Insert(TABLE_USERS, $record);
    }

    public function DeleteUserById($id) {
        Report::Trace(__METHOD__);

        if ($this->GetUserById($id) == null) {
            Report::Fail("User '$id' does not exist.");
            return false;
        }

        $record['id'] = $id;

        return $this->db->Delete(TABLE_USERS, $record);
    }

    /**
     * Courts
     */
    public function GetCourts() {
        $search['tournament_id'] = $this->tournamentId;
        return $this->db->Select(TABLE_COURTS, $search);
    }

    public function GetCourtById($id) {
        Report::Trace(__METHOD__);

        $search['id'] = $id;
        $search['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
        $results = $this->db->Select(TABLE_COURTS, $search);
        return $results[0];
    }

    public function GetCourtByName($name) {
        Report::Trace(__METHOD__);

        $search['name'] = $name;
        $search['tournament_id'] = $this->tournamentId;
        $results = $this->db->Select(TABLE_COURTS, $search);
        return $results[0];
    }

    public function InsertCourt($name) {
        Report::Trace(__METHOD__);

        $record['tournament_id'] = $this->tournamentId;
        $record['name'] = $name;
        return $this->db->Insert(TABLE_COURTS, $record);
    }

    public function DeleteCourtById($id) {
        Report::Trace(__METHOD__);

        if ($this->GetCourtById($id) == null) {
            Report::Fail("Court with id '$id' does not exist.");
            return false;
        }

        $record['id'] = $id;
        return $this->db->Delete(TABLE_COURTS, $record);
    }

    /**
     * Rounds
     */
    public function GetRounds() {
        $search['tournament_id'] = $this->tournamentId;
        return $this->db->Select(TABLE_ROUNDS, $search);
    }

    public function GetRoundById($id) {
        Report::Trace(__METHOD__);

        $search['id'] = $id;
        $search['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
        $results = $this->db->Select(TABLE_ROUNDS, $search);
        return $results[0];
    }

    public function InsertRound() {
        Report::Trace(__METHOD__);

        $record['tournament_id'] = $this->tournamentId;
        return $this->db->Insert(TABLE_ROUNDS, $record);
    }

    public function DeleteRoundById($id) {
        Report::Trace(__METHOD__);

        if ($this->GetRoundById($id) == null) {
            Report::Fail("Round with id '$id' does not exist.");
            return false;
        }

        $record['id'] = $id;
        return $this->db->Delete(TABLE_ROUNDS, $record);
    }

    /**
     * Players
     */
    public function GetPlayers() {
        Report::Trace(__METHOD__);

        $sql = "SELECT players.id, players.user_id, users.rating FROM `players`INNER JOIN `users` WHERE players.tournament_id = $this->tournamentId AND players.user_id = users.id";
        return $this->db->Query($sql);
    }

    public function InsertPlayer($userId) {
        Report::Trace(__METHOD__);

        $record['user_id'] = $userId;
        $record['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
        return $this->db->Insert(TABLE_PLAYERS, $record);
    }

    public function DeletePlayerByUserId($userId) {
        Report::Trace(__METHOD__);

        $record['user_id'] = $userId;
        $record['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
        return $this->db->Delete(TABLE_PLAYERS, $record);
    }

    /**
     * Matches
     */
    public function GetMatches() {
        Report::Trace(__METHOD__);

        $search['tournament_id'] = $this->tournamentId;
        return $this->db->Select(TABLE_MATCHES, $search);
    }

    public function GetMatchById($matchId) {
        Report::Trace(__METHOD__);

        $search['tournament_id'] = $this->tournamentId;
        $search['id'] = $matchId;
        $results = $this->db->Select(TABLE_MATCHES, $search);
        return $results[0];
    }

    public function InsertMatch($player1aId, $player1bId, $player2aId, $player2bId, $rating, $ratingDiff) {
        Report::Trace(__METHOD__);

        $record['tournament_id'] = $this->tournamentId;
        $record['player1a_id'] = $player1aId;
        $record['player1b_id'] = $player1bId;
        $record['player2a_id'] = $player2aId;
        $record['player2b_id'] = $player2bId;
        $record['rating'] = $rating;
        $record['rating_diff'] = $ratingDiff;
        return $this->db->Insert(TABLE_MATCHES, $record);
    }

    public function EnableAllMatches() {
        Report::Trace(__METHOD__);

        $record['disabled'] = 0;
        $this->db->Update(TABLE_MATCHES, $record);
    }

    public function DisableMatchById($matchId) {
        Report::Trace(__METHOD__);

        $table = TABLE_MATCHES;
        $sql = "UPDATE `$table` SET `disabled`= 1 WHERE `tournament_id`=$this->tournamentId AND `id` = $matchId";
        return $this->db->Query($sql);
    }

    public function DisableMatchesForPlayer($playerId) {
        Report::Trace(__METHOD__);

        $table = TABLE_MATCHES;
        $sql = "UPDATE `$table` SET `disabled`= 1 WHERE `tournament_id`=$this->tournamentId AND (`player1a_id` = $playerId OR `player1b_id` = $playerId OR `player2a_id` = $playerId OR `player2b_id` = $playerId)";

        return $this->db->Query($sql);
    }

    public function GetAvailableMatches() {
        Report::Trace(__METHOD__);

        $search['tournament_id'] = $this->tournamentId;
        $search['disabled'] = 0;
        return $this->db->Select(TABLE_MATCHES, $search);
    }

    public function SetPriorityForMatch($matchId, $priority) {
        Report::Trace(__METHOD__);

        $record['priority'] = $priority;
        $filter['id'] = $matchId;
        return $this->db->Update(TABLE_MATCHES, $record, $filter);
    }

    public function GetBestMatch() {
        Report::Trace(__METHOD__);

        $table = TABLE_MATCHES;
        $sql = "SELECT * FROM `$table`";
        $sql .= " WHERE `tournament_id` = $this->tournamentId AND `disabled` = 0";
        $sql .= " ORDER BY `priority` ASC, `rating` DESC";
        $sql .= " LIMIT 1";

        $matches = $this->db->Query($sql);

        if (count($matches) > 0)
            return $matches[0];

        return null;
    }

    /**
     * Schedule
     */
    public function GetSchedule() {
        Report::Trace(__METHOD__);

        $search['tournament_id'] = $this->tournamentId;
        return $this->db->Select(TABLE_SCHEDULE, $search);
    }

    public function DeleteSchedule() {
        Report::Trace(__METHOD__);

        $search['tournament_id'] = $this->tournamentId;
        $this->db->Delete(TABLE_SCHEDULE, $search);
    }

    public function GenerateSchedule() {
        Report::Trace(__METHOD__);

        $rounds = $this->GetRounds();
        $courts = $this->GetCourts();

        foreach ($rounds as $round) {
            foreach ($courts as $court) {
                $record['tournament_id'] = $this->tournamentId;
                $record['court_id'] = $court['id'];
                $record['round_id'] = $round['id'];

                if ($this->db->Insert(TABLE_SCHEDULE, $record) == false) {
                    Report::Fail("Unable add slot to schedule.");
                    return false;
                }
            }
        }

        return true;
    }

    public function SetSlotStateToClinic($slotId) {
        Report::Trace(__METHOD__);

        return $this->db->Query("UPDATE `schedule` SET `state`='clinic', `match_id`=NULL WHERE `id`=$slotId");
    }

    public function SetSlotStateToDisabled($slotId) {
        Report::Trace(__METHOD__);

        return $this->db->Query("UPDATE `schedule` SET `state`='disabled', `match_id`=NULL WHERE `id`=$slotId");
    }

    public function SetSlotStateToAvailable($slotId) {
        Report::Trace(__METHOD__);

        return $this->db->Query("UPDATE `schedule` SET `state`='available' WHERE `id`=$slotId");
    }

    public function AssignMatchToSlot($slotId, $matchId) {
        Report::Trace(__METHOD__);

        return $this->db->Query("UPDATE `schedule` SET `state`='available', `match_id`=$matchId WHERE `id`=$slotId");
    }

    public function DeleteMatchFromSlot($slotId) {
        Report::Trace(__METHOD__);

        return $this->db->Query("UPDATE `schedule` SET `state`='available', `match_id`=null WHERE `id`=$slotId");
    }

    public function GetSlotsByRoundId($roundId) {
        Report::Trace(__METHOD__);

        $search['tournament_id'] = $this->tournamentId;
        $search['round_id'] = $roundId;
        return $this->db->Select(TABLE_SCHEDULE, $search);
    }

    /**
     * Other
     */
    public function ResetPlanning() {
        Report::Trace(__METHOD__);

        return $this->db->Query("UPDATE `schedule` SET `match_id`=null WHERE `tournament_id`=$this->tournamentId AND `state`='available'");
    }

    public function GetMatchesForRound($roundId) {
        Report::Trace(__METHOD__);
        return $this->db->Query("SELECT matches.* FROM `matches` INNER JOIN `schedule` WHERE matches.id = schedule.match_id");
    }

    public function GetPlayerIdsForRound($roundId) {
        Report::Trace(__METHOD__);

        $matches = $this->GetMatchesForRound($roundId);
        $players = array();
        foreach ($matches as $match) {
            $players[] = $match['player1a'];
            $players[] = $match['player1b'];
            $players[] = $match['player2a'];
            $players[] = $match['player2b'];
        }

        return array_unique($players);
    }

    public function GetScheduleForPlayer($playerId) {
        Report::Trace(__METHOD__);

        $sql = "SELECT rounds.starttime as 'time',";
        $sql .= " courts.name as 'court', ";
        $sql .= " user1a.name as `player1a`, user1b.name as `player1b`, user2a.name as `player2a`, user2b.name as `player2b`";
        $sql .= " FROM `schedule`";
        $sql .= "  INNER JOIN `rounds` ON schedule.round_id = rounds.id";
        $sql .= "  INNER JOIN `courts` ON schedule.court_id = courts.id";
        $sql .= "  INNER JOIN `matches` ON schedule.match_id = matches.id";
        $sql .= "  INNER JOIN `players` as player1a ON matches.player1a_id = player1a.id";
        $sql .= "  INNER JOIN `players` as player1b ON matches.player1b_id = player1b.id";
        $sql .= "  INNER JOIN `players` as player2a ON matches.player2a_id = player2a.id";
        $sql .= "  INNER JOIN `players` as player2b ON matches.player2b_id = player2b.id";
        $sql .= "  INNER JOIN `users` as user1a ON player1a.user_id = user1a.id";
        $sql .= "  INNER JOIN `users` as user1b ON player1b.user_id = user1b.id";
        $sql .= "  INNER JOIN `users` as user2a ON player2a.user_id = user2a.id";
        $sql .= "  INNER JOIN `users` as user2b ON player2b.user_id = user2b.id";
        $sql .= " WHERE schedule.tournament_id = 1";
        $sql .= "  AND (player1a.id=$playerId OR player1b.id=$playerId OR player2a.id=$playerId OR player1b.id=$playerId)";

        Report::Debug($sql);

        return $this->db->Query($sql);
    }
}
