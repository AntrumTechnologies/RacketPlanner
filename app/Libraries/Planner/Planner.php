<?php

namespace App\Libraries\Planner;

require_once 'Report.php';
require_once 'DatabaseController.php';

use App\Libraries\Planner\Report;
use App\Libraries\Planner\DatabaseController;

use Illuminate\Support\Facades\Log;

class Planner
{
  private DatabaseController $dbc;
  private $playerMaxMatchCount = 5;

  private $desiredNumberOfIterations = 20;
  private $desiredPartnerRatingTolerance = 10.0;
  private $desiredTeamRatingTolerance = 4.0;
  private $doubleMatches = true;

  function __construct($tournamentId, $desiredNumberOfIterations, $desiredPartnerRatingTolerance, $desiredTeamRatingTolerance, 
    $doubleMatches, 
    $playerMaxMatchCount)
  {
    Report::Trace(__METHOD__);

    $this->dbc = new DatabaseController;
    $this->dbc->UseTournament($tournamentId);
    
    $this->desiredNumberOfIterations = $desiredNumberOfIterations;
    $this->desiredPartnerRatingTolerance = $desiredPartnerRatingTolerance;
    $this->desiredTeamRatingTolerance = $desiredTeamRatingTolerance;
    $this->doubleMatches = $doubleMatches;
    $this->playerMaxMatchCount = $playerMaxMatchCount;
  }

  // **************************************************************************
  // Match generation

  protected function DetermineMinMatchCount()
  {
    Report::Trace(__METHOD__);

    $slots = $this->dbc->GetSchedule();
    $minMatchCount = 0;
    foreach ($slots as $slot)
    {
      if ($slot['state'] == 'available')
        $minMatchCount++;
    }
    $minMatchCount *= 200;
    return $minMatchCount;
  }

  protected function GenerateTeams(&$players, $ratingTolerance, $doubles)
  {
    Report::Trace(__METHOD__);

    $size = count($players);
    $teams = array();
    if ($doubles == true) {
      // Generate teams for double matches
      for ($i1 = 0; $i1 < $size; $i1++)
      {
        for ($i2 = $i1 + 1; $i2 < $size; $i2++)
        {
          $player1 =& $players[$i1];
          $player2 =& $players[$i2];
          $diff = abs($player1['rating'] - $player2['rating']);
          if ($diff <= abs($ratingTolerance))
          {
            $team = array();
            $team['players'] = array();
            $team['players'][] = $player1['id'];
            $team['players'][] = $player2['id'];
            $team['rating'] = $player1['rating'] + $player2['rating'];
            $teams[] = $team;
          }
        }
      }
    } else {
      // Generate team for single matches
      for ($i1 = 0; $i1 < $size; $i1++)
      {
        $player1 =& $players[$i1];
        $team = array();
        $team['players'] = array();
        $team['players'][] = $player1['id'];
        $team['players'][] = $player1['id'];
        $team['rating'] = $player1['rating'] * 2;
        $teams[] = $team;
      }
    }

    return $teams;
  }

  protected function VerifyTeamRating(&$team1, &$team2, $ratingTolerance)
  {
    Report::Trace(__METHOD__);

    $diff = abs($team1['rating'] - $team2['rating']);
    if ($diff > abs($ratingTolerance))
      return false;

    $intersect = array_intersect($team1['players'], $team2['players']);
    if (count($intersect) > 0)
      return false;

    return true;
  }

  protected function VerifyPartnerCount(&$partnerCount, &$team, $maxPartnerCount)
  {
    Report::Trace(__METHOD__);

    $id0 = $team['players'][0];
    $id1 = $team['players'][1];

    if ($partnerCount[$id0][$id1] >= $maxPartnerCount)
      return false;

    return true;
  }

  protected function UpdatePartnerCount(&$partnerCount, &$team)
  {
    Report::Trace(__METHOD__);

    $id0 = $team['players'][0];
    $id1 = $team['players'][1];

    $partnerCount[$id0][$id1]++;
    $partnerCount[$id1][$id0]++;
  }


  protected function VerifyOpponentCount(&$opponentCounts, &$team1, &$team2, $maxOpponentCount)
  {
    Report::Trace(__METHOD__);

    foreach ($team1['players'] as $player1)
    {
      foreach ($team2['players'] as $player2)
      {
        if ($opponentCounts[$player1][$player2] >= $maxOpponentCount)
          return false;
      }
    }

    return true;
  }

  protected function UpdateOpponentCount(&$opponentCounts, &$team1, &$team2)
  {
    Report::Trace(__METHOD__);

    foreach ($team1['players'] as $player1)
    {
      foreach ($team2['players'] as $player2)
      {
        $opponentCounts[$player1][$player2]++;
        $opponentCounts[$player2][$player1]++;
      }
    }

    return true;
  }

  protected function TryGenerateMatches($partnerRatingTolerance, $teamRatingTolerance, $maxPartnerCount, $maxOpponentCount, $doubleMatches)
  {
    Report::Trace(__METHOD__);

    $this->dbc->DeleteAllMatches();

    $players = $this->dbc->GetPlayers();

    $partnerCounts = array();
    $opponentCounts = array();
    foreach ($players as &$player1)
    {
      $id1 = $player1['id'];
      foreach ($players as &$player2)
      {
        $id2 = $player2['id'];
        $partnerCounts[$id1][$id2] = 0;
        $opponentCounts[$id1][$id2] = 0;
      }
    }

    $teams = $this->GenerateTeams($players, $partnerRatingTolerance, $doubleMatches);

    $count = 0;
    foreach ($teams as $team1)
    {
      foreach ($teams as $team2)
      {
        $result = $this->VerifyTeamRating($team1, $team2, $teamRatingTolerance);
        $result &= $this->VerifyPartnerCount($partnerCounts, $team1, $maxPartnerCount);
        $result &= $this->VerifyPartnerCount($partnerCounts, $team2, $maxPartnerCount);
        $result &= $this->VerifyOpponentCount($opponentCounts, $team1, $team2, $maxOpponentCount);
        if ($result == true)
        {
          $this->UpdatePartnerCount($partnerCounts, $team1);
          $this->UpdatePartnerCount($partnerCounts, $team2);
          $this->UpdateOpponentCount($opponentCounts, $team1, $team2);

          $player1a = $team1['players'][0];
          $player1b = $team1['players'][1];
          $player2a = $team2['players'][0];
          $player2b = $team2['players'][1];
          $rating = $team1['rating'] + $team2['rating'];
          $ratingDiff = abs($team1['rating'] - $team2['rating']);
          $this->dbc->InsertMatch($player1a, $player1b, $player2a, $player2b, $rating, $ratingDiff);
          $count++;
        }
      }
    }
    return $count;
  }

  public function GenerateMatches()
  {
    Report::Trace(__METHOD__);

    // Reset planning before matches can be deleted.
    $this->dbc->ResetPlanning();
    $required = $this->DetermineMinMatchCount();

    $count = 0;
    for ($i = 1; $i < ($this->desiredNumberOfIterations * 5); $i += 5)
    {
      if ($this->doubleMatches == true) {
        $maxOpponentCount = $i;
      } else {
        $maxOpponentCount = 1;
      }
      $count = $this->TryGenerateMatches($this->desiredPartnerRatingTolerance, $this->desiredTeamRatingTolerance, $i, $maxOpponentCount, $this->doubleMatches);
      Report::Info("Number of matches generated: $count");
      if ($count >= $required)
        break;
    }
  }

  // **************************************************************************
  // Planner implementation

  protected function GetPlayersPriority()
  {
    // Players part of a small rating category should have higher priorities

    $players = $this->dbc->GetPlayers();

    // Build category list
    $categories = array();
    foreach ($players as $player)
    {
      $rating = $player['rating'];
      $ratingCat = floor($rating);
      if (isset($categories[$ratingCat]))
      {
        $categories[$ratingCat]++;
      }
      else
      {
        $categories[$ratingCat] = 1;
      }
    }

    // Set player priorities
    $priorities = array();
    foreach ($players as $player)
    {
      $rating = $player['rating'];
      $ratingCat = floor($rating);

      $priorities[$player['id']] = $categories[$ratingCat];
    }

    return $priorities;
  }


  protected function GetMatchCountsForPlayers()
  {
    Report::Trace(__METHOD__);

    $players = $this->dbc->GetPlayers();
    foreach ($players as $player)
      $matchCounts[$player['id']] = 0;

    $clinicRounds = array();
    $slots = $this->dbc->GetSchedule(); foreach ($slots as $slot)
    {
      if ($slot['state'] == 'clinic')
      {
        $clinicRounds[$slot['round_id']] = 1;
      }

      if ($slot['state'] == 'available')
      {
        $match_id = $slot['match_id'];
        if ($match_id != null)
        {
          $match = $this->dbc->GetMatchById($match_id);
          $matchCounts[$match['player1a_id']]++;
          $matchCounts[$match['player1b_id']]++;
          $matchCounts[$match['player2a_id']]++;
          $matchCounts[$match['player2b_id']]++;
        }
      }
    }

    // Add clinic rounds to matchCount of players participating in clinic
    $numberOfClinicRounds = count($clinicRounds);
    foreach ($players as $player)
    {
      if ($player['clinic'] == 1)
        $matchCounts[$player['id']] += $numberOfClinicRounds;
    }

    return $matchCounts;
  }

  private function GetMatesCountsForPlayers()
  {
    $matesCounts = array();

    $players = $this->dbc->GetPlayers();

    foreach ($players as $player1)
    {
      $id1 = $player1['id'];
      foreach ($players as $player2)
      {
        $id2 = $player2['id'];
        $matesCounts[$id1][$id2] = 0;
        $matesCounts[$id2][$id1] = 0;
      }
    }

    $slots = $this->dbc->GetSchedule();
    foreach ($slots as $slot)
    {
      $matchId = $slot['match_id'];
      if ($matchId != NULL)
      {
        $match = $this->dbc->GetMatchById($matchId);

        $id1 = $match['player1a_id'];
        $id2 = $match['player1b_id'];
        $id3 = $match['player2a_id'];
        $id4 = $match['player2b_id'];

        $matesCounts[$id1][$id2]++;
        $matesCounts[$id1][$id3]++;
        $matesCounts[$id1][$id4]++;

        $matesCounts[$id2][$id1]++;
        $matesCounts[$id2][$id3]++;
        $matesCounts[$id2][$id4]++;

        $matesCounts[$id3][$id1]++;
        $matesCounts[$id3][$id2]++;
        $matesCounts[$id3][$id4]++;

        $matesCounts[$id4][$id1]++;
        $matesCounts[$id4][$id2]++;
        $matesCounts[$id4][$id3]++;
      }
    }
    return $matesCounts;
  }


  protected function DisablePlayersWithMaxMatchCount($matchCounts)
  {
    foreach ($matchCounts as $playerId => $count)
    {
      if ($count >= $this->playerMaxMatchCount)
        $this->dbc->DisableMatchesForPlayer($playerId);
    }
  }

  protected function DisableMatchesForNotPresentPlayers()
  {
    $players = $this->dbc->GetNotPresentPlayers();
    foreach ($players as $player)
    {
      $this->dbc->DisableMatchesForPlayer($player['id']);
    }
  }

  protected function DisableMatchesForPlayersOfRound($roundId)
  {
    Report::Trace(__METHOD__);

    $playerIds = array();

    $matches = $this->dbc->GetMatchesForRound($roundId);
    foreach ($matches as $match)
    {
      $playerIds[] = $match['player1a_id'];
      $playerIds[] = $match['player1b_id'];
      $playerIds[] = $match['player2a_id'];
      $playerIds[] = $match['player2b_id'];
    }

    $slots = $this->dbc->GetSlotsByRoundId($roundId);
    $clinic = false;
    foreach ($slots as $slot)
    {
      if ($slot['state'] == 'clinic')
        $clinic = true;
    }

    if ($clinic == true)
    {
      $clinicPlayers = $this->dbc->GetPlayersForClinic();
      foreach ($clinicPlayers as $player)
      {
        $playerIds[] = $player['id'];
      }
    }

    $playerIds = array_unique($playerIds);
    foreach ($playerIds as $playerId)
    {
      $this->dbc->DisableMatchesForPlayer($playerId);
    }
  }

  protected function DisableMatchesFromSchedule()
  {
    Report::Trace(__METHOD__);

    $slots = $this->dbc->GetSchedule();
    foreach ($slots as $slot)
    {
      if ($slot['match_id'] != null)
        $this->dbc->DisableMatchById($slot['match_id']);
    }
  }

  protected function SetMatchPriority($matchCounts, $matesCounts)
  {
    Report::Trace(__METHOD__);

    $playerPriorities = $this->GetPlayersPriority();

    $matches = $this->dbc->GetAvailableMatches();
    foreach ($matches as $match)
    {
      $playerIds = array();
      $playerIds[] = $match['player1a_id'];
      $playerIds[] = $match['player1b_id'];
      $playerIds[] = $match['player2a_id'];
      $playerIds[] = $match['player2b_id'];

      $priority = 0;

      foreach ($playerIds as $id1)
      {
        $count = $matchCounts[$id1];
        $priority += $count * 100;
        $priority += $playerPriorities[$id1] * 10;
        foreach ($playerIds as $id2)
        {
          $priority += $matesCounts[$id1][$id2] * 20;
        }
      }
      $this->dbc->SetPriorityForMatch($match['id'], $priority);
    }
  }

  protected function DisableMatchesForPlayersWithBreak($roundId)
  {
    $players = $this->dbc->GetPlayers();
    foreach ($players as $player)
    {
      if ($player['break_round_id'] == $roundId)
        $this->dbc->DisableMatchesForPlayer($player['id']);
    }
  }

  public function SetMaxMatchCountForPlayers($maxCount)
  {
    $this->playerMaxMatchCount = $maxCount;
  }

  public function PlanSlot($slotId)
  {
    Report::Trace(__METHOD__);
    Report::Info("Plannig slot $slotId");

    $slot = $this->dbc->GetSlotById($slotId);

    if ($slot['state'] != 'available')
    {
      Report::Warning("Slot $slotId is not available for planning.");
      return;
    }

    if ($slot['match_id'] != NULL)
    {
      Report::Warning("Slot $slotId is already planned.");
      return;
    }

    $this->dbc->EnableAllMatches();
    $nom = count($this->dbc->GetAvailableMatches());
    Report::Warning("Available matches : $nom");

    $this->DisableMatchesFromSchedule(); // Don't allow duplicate matches in schedule
    $this->DisableMatchesForNotPresentPlayers();
    $this->DisableMatchesForPlayersOfRound($slot['round_id']);
    $this->DisableMatchesForPlayersWithBreak($slot['round_id']);

    $matchCounts = $this->GetMatchCountsForPlayers();
    $this->DisablePlayersWithMaxMatchCount($matchCounts);

    $matesCounts = $this->GetMatesCountsForPlayers();
    $this->SetMatchPriority($matchCounts, $matesCounts);

    $match = $this->dbc->GetBestMatch();

    if ($match == null)
    {
      Report::Warning("No match found to plan!");
      return;
    }

    $this->dbc->AssignMatchToSlot($slot['id'], $match['id']);
  }

  public function PlanRound($roundId)
  {
    Report::Trace(__METHOD__);

    Report::Info("Planning round $roundId");

    $slots = $this->dbc->GetSlotsByRoundId($roundId);
    foreach ($slots as $slot)
    {
      $this->PlanSlot($slot['id']);
    }
  }

  public function PlanSchedule()
  {
    Report::Trace(__METHOD__);

    $rounds = $this->dbc->GetRounds();
    foreach ($rounds as $round)
    {
      $this->PlanRound($round['id']);
    }
  }

  public function ReportStatistics()
  {
    $matesCounts = $this->GetMatesCountsForPlayers();
    $matchCounts = $this->GetMatchCountsForPlayers();

    Report::Info("Mates count:");

    $players = $this->dbc->GetPlayers();
    foreach ($players as $player)
    {
      $playerList[$player['id']] = $player;
    }

    foreach ($matesCounts as $id1 => $row)
    {
      $count = $matchCounts[$id1];
      $player = $playerList[$id1];
      $name = $player['name'];
      $rating = $player['rating'];
      $txt = "$count ; $rating ; $name ;";
      foreach ($row as $id2 => $count)
      {
        if ($count > 1)
          $txt .= " $count x [$id2] ;";
      }
      Report::Info($txt);
    }
  }

}