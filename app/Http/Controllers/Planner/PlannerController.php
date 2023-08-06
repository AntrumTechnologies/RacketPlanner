<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Planner\DatabaseController;
use App\Models\Player;
use App\Models\Schedule;
use Illuminate\Http\Request;

class PlannerController extends Controller
{
    public function __construct(protected DatabaseController $dbc)
    {
        $this->middleware('auth');
    }

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
		$minMatchCount *= 20;
		return $minMatchCount;
	}

	protected function GenerateTeams(&$players, $ratingTolerance)
	{
		Report::Trace(__METHOD__);

		$size = count($players);
		$teams = array();
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

	protected function TryGenerateMatches($partnerRatingTolerance, $teamRatingTolerance, $maxPartnerCount, $maxOpponentCount)
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

		$teams = $this->GenerateTeams($players, $partnerRatingTolerance);

		$count = 0;
		foreach ($teams as $team1)
		{
			foreach ($teams as $team2)
			{
				$result = $this->VerifyTeamRating($team1, $team2, $teamRatingTolerance);
				$result &= $this->VerifyPartnerCount($partnerCounts, $team1, $maxPartnerCount);
				$result &= $this->VerifyPartnerCount($partnerCounts, $team2, $maxPartnerCount);
				//$result &= $this->VerifyOpponentCount($opponentCounts, $team1, $team2, $maxOpponentCount);
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
		for ($i = 1; $i < 50; $i += 5)
		{
			$count = $this->TryGenerateMatches(3.0, 1.0, $i, $i);
			Report::Info("Number of matches generated: $count");
			if ($count >= $required)
				break;
		}

	}

	// Generate planning:
	// 1. Detirmine the amount of planned matches for each player
	// Rounds with clinic will count as match for players that join the clinic
	// Disable matches for players already planned in this round
	// 

	protected function GetMatchCountsForPlayers()
	{
		Report::Trace(__METHOD__);

		$players = $this->dbc->GetPlayers();
		foreach ($players as $player)
			$matchCounts[$player['id']] = 0;

		$slots = $this->dbc->GetSchedule();
		$clinicRounds = array(); foreach ($slots as $slot)
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
		return $matchCounts;
	}

	protected function DisablePlayersWithMatchCount($matchCounts, $maxCount)
	{
		foreach ($matchCounts as $playerId => $count)
		{
			if ($count >= $maxCount)
				$this->dbc->DisableMatchesForPlayer($playerId);
		}
	}

	protected function DisableMatchesForPlayersOfRound($roundId)
	{
		Report::Trace(__METHOD__);

		$playerIds = $this->dbc->GetPlayerIdsForRound($roundId);
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
	protected function SetMatchPriority($matchCounts)
	{
		Report::Trace(__METHOD__);

		$matches = $this->dbc->GetAvailableMatches();
		foreach ($matches as $match)
		{
			$priority = 0;
			$priority += $matchCounts[$match['player1a_id']];
			$priority += $matchCounts[$match['player1b_id']];
			$priority += $matchCounts[$match['player2a_id']];
			$priority += $matchCounts[$match['player2b_id']];
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

	public function PlanSlot($slot)
	{
		Report::Trace(__METHOD__);

		$this->dbc->EnableAllMatches();
		$this->DisableMatchesFromSchedule(); // Don't allow duplicate matches in schedule
		$this->DisableMatchesForPlayersOfRound($slot['round_id']);
		$this->DisableMatchesForPlayersWithBreak($slot['round_id']);

		$matchCounts = $this->GetMatchCountsForPlayers();
		$this->DisablePlayersWithMatchCount($matchCounts, 5);
		$this->SetMatchPriority($matchCounts);

		$match = $this->dbc->GetBestMatch();

		if ($match == null)
		{
			Report::Fail("No match found to plan!");
			return;
		}

		$this->dbc->AssignMatchToSlot($slot['id'], $match['id']);
	}

	public function PlanRound($round)
	{
		Report::Trace(__METHOD__);

		$id = $round['id'];
		Report::Info("Planning round $id");

		$slots = $this->dbc->GetSlotsByRoundId($round['id']);
		foreach ($slots as $slot)
		{
			if ($slot['match_id'] == null)
				$this->PlanSlot($slot);
		}
	}

	public function PlanSchedule()
	{
		Report::Trace(__METHOD__);

		$rounds = $this->dbc->GetRounds();
		foreach ($rounds as $round)
		{
		    $this->PlanRound($round);
		}

		// Report number of matches per player
		Report::Info("Match counts:");
		$matchCounts = $this->GetMatchCountsForPlayers();
		foreach ($matchCounts as $id => $count)
		{
			Report::Info("[$id] = $count");
		}
	}
}
