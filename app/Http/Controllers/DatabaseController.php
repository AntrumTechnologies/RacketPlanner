<?php

namespace App\Http\Controllers;

use App\Model\Report;
use Illuminate\Http\Request;

class DatabaseController extends Controller
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
}
