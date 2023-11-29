<?php

namespace App\Libraries\Planner;

require_once 'Report.php';
require_once 'Database.php';

use App\Libraries\Planner\Report;
use App\Libraries\Planner\Database;
use DateTime;

define("TABLE_TOURNAMENTS", 'tournaments');
define("TABLE_USERS", 'users');
define("TABLE_COURTS", 'courts');
define("TABLE_ROUNDS", 'rounds');
define("TABLE_PLAYERS", 'players');
define("TABLE_MATCHES", 'matches');
define("TABLE_SCHEDULES", 'schedules');

class DatabaseController
{
  private Database $db;
  private $tournamentId;

  function __construct()
  {
    $this->db = new Database;
    Report::Trace(__METHOD__);
  }

  protected function ResetTable($table)
  {
    $this->db->Delete($table);
    $this->db->Query("ALTER TABLE `$table` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
  }

  public function ResetDatabase()
  {
    $this->ResetTable(TABLE_SCHEDULES);
    $this->ResetTable(TABLE_MATCHES);
    $this->ResetTable(TABLE_PLAYERS);
    $this->ResetTable(TABLE_USERS);
    $this->ResetTable(TABLE_ROUNDS);
    $this->ResetTable(TABLE_COURTS);
    $this->ResetTable(TABLE_TOURNAMENTS);
  }

  // *********************
  // Tournament

  public function UseTournament($id)
  {
    Report::Trace(__METHOD__);

    if ($this->GetTournamentById($id) == null)
    {
      Report::Fail("Unable to use tournament with id: $id");
      return false;
    }

    Report::Info("Using tournament with id: $id");
    $this->tournamentId = $id;
    return true;
  }

  public function GetTournaments()
  {
    Report::Trace(__METHOD__);

    return $this->db->Select(TABLE_TOURNAMENTS);
  }

  public function GetTournamentById($id)
  {
    Report::Trace(__METHOD__);

    $search['id'] = $id;
    $results = $this->db->Select(TABLE_TOURNAMENTS, $search);
    return $results[0];
  }

  public function GetTournamentByName($name)
  {
    Report::Trace(__METHOD__);

    $search['name'] = $name;
    $results = $this->db->Select(TABLE_TOURNAMENTS, $search);
    return $results[0];
  }

  public function InsertTournament($name)
  {
    Report::Trace(__METHOD__);

    $record['name'] = $name;

    return $this->db->Insert(TABLE_TOURNAMENTS, $record);
  }

  public function DeleteTournamentById($id)
  {
    Report::Trace(__METHOD__);

    if ($this->GetTournamentById($id) == null)
    {
      Report::Fail("Tournament '$id' does not exist.");
      return false;
    }

    $record['id'] = $id;

    if ($this->db->Delete(TABLE_TOURNAMENTS, $record) == false)
    {
      Report::Fail("Tournament '$id' could not be deleted.");
      return false;
    }

    return true;
  }

  // *********************
  // Users

  public function GetUsers()
  {
    Report::Trace(__METHOD__);

    return $this->db->Select(TABLE_USERS);
  }

  public function GetUserById($id)
  {
    Report::Trace(__METHOD__);

    $search['id'] = $id;
    $results = $this->db->Select(TABLE_USERS, $search);
    return $results[0];
  }

  public function GetUserByName($name)
  {
    Report::Trace(__METHOD__);

    $search['name'] = $name;
    $results = $this->db->Select(TABLE_USERS, $search);
    return $results[0];
  }

  public function InsertUser($name, $email, $rating)
  {
    Report::Debug(__METHOD__);

    $record['name'] = $name;
    $record['email'] = $email;
    $record['rating'] = $rating;
    return $this->db->Insert(TABLE_USERS, $record);
  }

  public function DeleteUserById($id)
  {
    Report::Trace(__METHOD__);

    if ($this->GetUserById($id) == null)
    {
      Report::Fail("User '$id' does not exist.");
      return false;
    }

    $record['id'] = $id;

    return $this->db->Delete(TABLE_USERS, $record);
  }

  // *********************
  // Courts

  public function GetCourts()
  {
    $search['tournament_id'] = $this->tournamentId;
    return $this->db->Select(TABLE_COURTS, $search);
  }

  public function GetCourtById($id)
  {
    Report::Trace(__METHOD__);

    $search['id'] = $id;
    $search['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
    $results = $this->db->Select(TABLE_COURTS, $search);
    return $results[0];
  }

  public function GetCourtByName($name)
  {
    Report::Trace(__METHOD__);

    $search['name'] = $name;
    $search['tournament_id'] = $this->tournamentId;
    $results = $this->db->Select(TABLE_COURTS, $search);
    return $results[0];
  }

  public function InsertCourt($name)
  {
    Report::Trace(__METHOD__);

    $record['tournament_id'] = $this->tournamentId;
    $record['name'] = $name;
    $result = $this->db->Insert(TABLE_COURTS, $record);

    $this->UpdateSchedule();

    return $result;

  }

  public function DeleteCourtById($id)
  {
    Report::Trace(__METHOD__);

    if ($this->GetCourtById($id) == null)
    {
      Report::Fail("Court with id '$id' does not exist.");
      return false;
    }

    // Remove from schedule before it can be removed from the rounds table
    $this->DeleteSlotsForCourt($id);

    $record['id'] = $id;
    return $this->db->Delete(TABLE_COURTS, $record);
  }

  // *********************
  // Rounds

  public function GetRounds()
  {
    $search['tournament_id'] = $this->tournamentId;
    return $this->db->Select(TABLE_ROUNDS, $search);
  }

  public function GetRoundById($id)
  {
    Report::Trace(__METHOD__);

    $search['id'] = $id;
    $search['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
    $results = $this->db->Select(TABLE_ROUNDS, $search);
    return $results[0];
  }

  public function InsertRound(DateTime $startTime, DateTime $endTime)
  {
    Report::Trace(__METHOD__);

    $record['tournament_id'] = $this->tournamentId;
    $record['starttime'] = $startTime->format('Y-m-d H:i:s');
    $record['endtime'] = $endTime->format('Y-m-d H:i:s');
    $result = $this->db->Insert(TABLE_ROUNDS, $record);

    $this->UpdateSchedule();

    return $result;
  }

  public function DeleteRoundById($id)
  {
    Report::Trace(__METHOD__);

    if ($this->GetRoundById($id) == null)
    {
      Report::Fail("Round with id '$id' does not exist.");
      return false;
    }

    // Remove from schedule before it can be removed from the rounds table
    $this->DeleteSlotsForRound($id);

    $record['id'] = $id;
    return $this->db->Delete(TABLE_ROUNDS, $record);
  }

  // *********************
  // Players

  public function GetPlayers()
  {
    Report::Trace(__METHOD__);

    $sql = "SELECT users.name, players.* FROM `players`INNER JOIN `users` WHERE players.tournament_id = $this->tournamentId AND players.user_id = users.id";
    return $this->db->Query($sql);
  }

  public function GetPresentPlayers()
  {
    Report::Trace(__METHOD__);

    $sql = "SELECT users.name, players.* FROM `players`INNER JOIN `users` WHERE players.tournament_id = $this->tournamentId AND players.user_id = users.id AND players.present = 1";
    return $this->db->Query($sql);
  }

  public function GetNotPresentPlayers()
  {
    Report::Trace(__METHOD__);

    $sql = "SELECT users.name, players.* FROM `players`INNER JOIN `users` WHERE players.tournament_id = $this->tournamentId AND players.user_id = users.id AND players.present = 0";
    return $this->db->Query($sql);
  }


  public function InsertPlayer($userId)
  {
    Report::Trace(__METHOD__);

    $record['user_id'] = $userId;
    $record['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
    return $this->db->Insert(TABLE_PLAYERS, $record);
  }

  public function DeletePlayerByUserId($userId)
  {
    Report::Trace(__METHOD__);

    $record['user_id'] = $userId;
    $record['tournament_id'] = $this->tournamentId; // Do not allow getting courts from other tournaments
    return $this->db->Delete(TABLE_PLAYERS, $record);
  }

  public function SetPlayerToPresent($playerId)
  {
    $record['present'] = 1;
    $filter['id'] = $playerId;
    return $this->db->Update(TABLE_PLAYERS, $record, $filter);
  }
  public function SetPlayerToNotPresent($playerId)
  {
    $record['present'] = 0;
    $filter['id'] = $playerId;
    return $this->db->Update(TABLE_PLAYERS, $record, $filter);
  }

  public function AddPlayerToClinic($playerId)
  {
    $record['clinic'] = 1;
    $filter['id'] = $playerId;
    return $this->db->Update(TABLE_PLAYERS, $record, $filter);
  }

  public function RemovePlayerFromClinic($playerId)
  {
    $record['clinic'] = 0;
    $filter['id'] = $playerId;
    return $this->db->Update(TABLE_PLAYERS, $record, $filter);
  }

  public function GetPlayersForClinic()
  {
    $sql = "SELECT players.id, players.user_id FROM `players`INNER JOIN `users` WHERE players.tournament_id = $this->tournamentId AND players.user_id = users.id AND players.clinic = 1";
    return $this->db->Query($sql);
  }

  // *********************
  // Matches

  public function GetMatches()
  {
    Report::Trace(__METHOD__);

    $search['tournament_id'] = $this->tournamentId;
    return $this->db->Select(TABLE_MATCHES, $search);
  }

  public function GetMatchById($matchId)
  {
    Report::Trace(__METHOD__);

    $search['tournament_id'] = $this->tournamentId;
    $search['id'] = $matchId;
    $results = $this->db->Select(TABLE_MATCHES, $search);
    return $results[0];
  }

  public function InsertMatch($player1aId, $player1bId, $player2aId, $player2bId, $rating, $ratingDiff)
  {
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

  public function DeleteAllMatches()
  {
    $filter['tournament_id'] = $this->tournamentId;
    $this->db->Delete(TABLE_MATCHES, $filter);
  }

  public function EnableAllMatches()
  {
    Report::Trace(__METHOD__);

    $record['disabled'] = 0;

    $this->db->Update(TABLE_MATCHES, $record);
  }

  public function DisableMatchById($matchId)
  {
    Report::Trace(__METHOD__);

    $table = TABLE_MATCHES;
    $sql = "UPDATE `$table` SET `disabled`= 1 WHERE `tournament_id`=$this->tournamentId AND `id` = $matchId";
    return $this->db->Query($sql);
  }

  public function DisableMatchesForPlayer($playerId)
  {
    Report::Trace(__METHOD__);

    $table = TABLE_MATCHES;
    $sql = "UPDATE `$table` SET `disabled`= 1 WHERE `tournament_id`=$this->tournamentId AND (`player1a_id` = $playerId OR `player1b_id` = $playerId OR `player2a_id` = $playerId OR `player2b_id` = $playerId)";

    return $this->db->Query($sql);
  }

  public function GetAvailableMatches()
  {
    Report::Trace(__METHOD__);

    $search['tournament_id'] = $this->tournamentId;
    $search['disabled'] = 0;

    return $this->db->Select(TABLE_MATCHES, $search);
  }

  public function SetPriorityForMatch($matchId, $priority)
  {
    Report::Trace(__METHOD__);

    $record['priority'] = $priority;

    $filter['id'] = $matchId;

    return $this->db->Update(TABLE_MATCHES, $record, $filter);
  }

  public function GetBestMatch()
  {
    Report::Trace(__METHOD__);

    $table = TABLE_MATCHES;
    $sql = "SELECT * FROM `$table`";
    $sql .= " WHERE `tournament_id` = $this->tournamentId AND `disabled` = 0";
    $sql .= " ORDER BY `priority` ASC, `rating_diff` ASC, `rating` DESC";
    $sql .= " LIMIT 1";

    $matches = $this->db->Query($sql);

    if (count($matches) > 0)
      return $matches[0];

    return null;
  }

  // *********************
  // Schedule

  public function GetSchedule()
  {
    Report::Trace(__METHOD__);

    $search['tournament_id'] = $this->tournamentId;

    return $this->db->Select(TABLE_SCHEDULES, $search);
  }

  public function DeleteSchedule()
  {
    Report::Trace(__METHOD__);

    $search['tournament_id'] = $this->tournamentId;

    $this->db->Delete(TABLE_SCHEDULES, $search);
  }

  protected function DeleteSlotsForRound($roundId)
  {
    // Remove round from schedule first
    $record['round_id'] = $roundId;
    $this->db->Delete(TABLE_SCHEDULES, $record);
  }

  protected function DeleteSlotsForCourt($courtId)
  {
    // Remove round from schedule first
    $record['court_id'] = $courtId;
    $this->db->Delete(TABLE_SCHEDULES, $record);
  }

  protected function UpdateSchedule()
  {
    Report::Trace(__METHOD__);

    $slots = $this->GetSchedule();
    $rounds = $this->GetRounds();
    $courts = $this->GetCourts();

    // Create slots for new rounds & courts
    foreach ($rounds as $round)
    {
      $roundId = $round['id'];
      foreach ($courts as $court)
      {
        $courtId = $court['id'];
        $exists = false;
        foreach ($slots as $slot)
        {
          if (($slot['round_id'] == $roundId) && ($slot['court_id'] == $courtId))
          {
            $exists = true;
            break;
          }
        }
        if ($exists == false)
        {
          $record['tournament_id'] = $this->tournamentId;
          $record['round_id'] = $roundId;
          $record['court_id'] = $courtId;
          if ($this->db->Insert(TABLE_SCHEDULES, $record) == false)
          {
            Report::Fail("Unable add slot to schedule.");
            return false;
          }
        }
      }
    }

    return true;
  }

  public function GetSlotById($slotId)
  {
    Report::Trace(__METHOD__);

    $filter['id'] = $slotId;

    $results = $this->db->Select(TABLE_SCHEDULES, $filter);
    return $results[0];
  }

  public function SetSlotStateToClinic($slotId)
  {
    Report::Trace(__METHOD__);

    $record['state'] = 'clinic';
    $record['match_id'] = NULL;

    $filter['id'] = $slotId;

    return $this->db->Update(TABLE_SCHEDULES, $record, $filter);
  }

  public function SetSlotStateToDisabled($slotId)
  {
    Report::Trace(__METHOD__);

    $record['state'] = 'disabled';
    $record['match_id'] = NULL;

    $filter['id'] = $slotId;

    return $this->db->Update(TABLE_SCHEDULES, $record, $filter);
  }

  public function SetSlotStateToAvailable($slotId)
  {
    Report::Trace(__METHOD__);

    $record['state'] = 'available';

    $filter['id'] = $slotId;

    return $this->db->Update(TABLE_SCHEDULES, $record, $filter);
  }

  public function AssignMatchToSlot($slotId, $matchId)
  {
    Report::Trace(__METHOD__);

    $record['state'] = 'available';
    $record['match_id'] = $matchId;

    $filter['id'] = $slotId;

    return $this->db->Update(TABLE_SCHEDULES, $record, $filter);
  }

  public function DeleteMatchFromSlot($slotId)
  {
    Report::Trace(__METHOD__);

    $record['state'] = 'available';
    $record['match_id'] = NULL;

    $filter['id'] = $slotId;

    return $this->db->Update(TABLE_SCHEDULES, $record, $filter);
  }

  public function GetSlotsByRoundId($roundId)
  {
    Report::Trace(__METHOD__);

    $search['tournament_id'] = $this->tournamentId;
    $search['round_id'] = $roundId;

    return $this->db->Select(TABLE_SCHEDULES, $search);
  }

  // *********************
  // Other

  public function ResetPlanning()
  {
    Report::Trace(__METHOD__);

    $record['match_id'] = NULL;

    $filter['tournament_id'] = $this->tournamentId;
    //$filter['state'] = 'available';

    return $this->db->Update(TABLE_SCHEDULES, $record, $filter);
  }

  public function GetMatchesForRound($roundId)
  {
    Report::Trace(__METHOD__);
    return $this->db->Query("SELECT matches.* FROM `matches` INNER JOIN `schedules` WHERE matches.id = schedules.match_id AND schedules.round_id = $roundId");
  }

  public function GetScheduleForPlayer($playerId)
  {
    Report::Trace(__METHOD__);

    $sql = "SELECT rounds.starttime as 'time',";
    $sql .= " courts.name as 'court', ";
    $sql .= " user1a.name as `player1a`, user1b.name as `player1b`, user2a.name as `player2a`, user2b.name as `player2b`";
    $sql .= " FROM `schedules`";
    $sql .= "  INNER JOIN `rounds` ON schedules.round_id = rounds.id";
    $sql .= "  INNER JOIN `courts` ON schedules.court_id = courts.id";
    $sql .= "  INNER JOIN `matches` ON schedules.match_id = matches.id";
    $sql .= "  INNER JOIN `players` as player1a ON matches.player1a_id = player1a.id";
    $sql .= "  INNER JOIN `players` as player1b ON matches.player1b_id = player1b.id";
    $sql .= "  INNER JOIN `players` as player2a ON matches.player2a_id = player2a.id";
    $sql .= "  INNER JOIN `players` as player2b ON matches.player2b_id = player2b.id";
    $sql .= "  INNER JOIN `users` as user1a ON player1a.user_id = user1a.id";
    $sql .= "  INNER JOIN `users` as user1b ON player1b.user_id = user1b.id";
    $sql .= "  INNER JOIN `users` as user2a ON player2a.user_id = user2a.id";
    $sql .= "  INNER JOIN `users` as user2b ON player2b.user_id = user2b.id";
    $sql .= " WHERE schedules.tournament_id = 1";
    $sql .= "  AND (player1a.id=$playerId OR player1b.id=$playerId OR player2a.id=$playerId OR player1b.id=$playerId)";

    Report::Debug($sql);

    return $this->db->Query($sql);
  }

}