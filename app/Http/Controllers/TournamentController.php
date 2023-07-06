<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\MatchDetails;
use App\Models\Tournament;
use App\Models\TournamentCourt;
use App\Models\TournamentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function index() {
        $tournaments = Tournament::all();
        
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        foreach ($tournaments as $tournament) {
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
        }

        return view('tournaments', ['tournaments' => $tournaments]);
    }

    public function show($id) {
        $tournament = Tournament::findOrFail($id);
        // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
        $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
        $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));

        $tournamentMatches = DB::table('matches')->where('tournament', $id)
            ->leftJoin('users as player1', 'player1.id', '=', 'matches.player1')
            ->leftJoin('users as player2', 'player2.id', '=', 'matches.player2')
            ->leftJoin('users as player3',  function ($f) {
                $f->on('player3.id', '=', 'matches.player3')->whereNotNull('player3.id');
            })
            ->leftJoin('users as player4',  function ($f) {
                $f->on('player4.id', '=', 'matches.player4')->whereNotNull('player4.id');
            })
            ->leftJoin('courts', 'courts.id', '=', 'matches.court')
            ->select('matches.id',
                'courts.name as court',
                'matches.datetime',
                'matches.score1_2',
                'matches.score3_4',
                'player1.name as player1',
                'player1.id as player1_id',
                'player2.name as player2',
                'player2.id as player2_id',
                'player3.name as player3',
                'player3.id as player3_id',
                'player4.name as player4',
                'player4.id as player4_id',)
            ->get();
        $tournamentMatches = $tournamentMatches->groupBy('datetime');

        $tournamentCourts = DB::table('tournaments_courts')->where('tournament', $id)->join('courts', 'courts.id', '=', 'tournaments_courts.court')->get();

        $tournamentUsers = DB::table('tournaments_users')->where('tournament', $id)->join('users', 'users.id', '=', 'tournaments_users.user')->get();

        return view('tournament', ['tournament' => $tournament, 'tournamentMatches' => $tournamentMatches, 'tournamentCourts' => $tournamentCourts, 'tournamentUsers' => $tournamentUsers]);
    }

    public function showDetails($id) {
        $tournament = Tournament::findOrFail($id);
        
        return view('tournament-details', ['tournament' => $tournament]);
    }

    public function tournamentCourts($id) {
        $tournament = Tournament::findOrFail($id);

        $tournamentCourts = DB::table('tournaments_courts')->where('tournament', $id)->join('courts', 'courts.id', '=', 'tournaments_courts.court')->get();

        // Only return courts that are not yet assigned to the given tournament
        $courtIds = TournamentCourt::where('tournament', $id)->pluck('court')->all();
        $courts = Court::whereNotIn('id', $courtIds)->get();

        return view('tournament-courts', ['tournament' => $tournament, 'tournamentCourts' => $tournamentCourts, 'courts' => $courts]);
    }

    public function tournamentUsers($id) {
        $tournament = Tournament::findOrFail($id);

        $tournamentUsers = DB::table('tournaments_users')->where('tournament', $id)->join('users', 'users.id', '=', 'tournaments_users.user')->get();

        // Only return users that are not yet assigned to the given tournament
        $userIds = TournamentUser::where('tournament', $id)->pluck('user')->all();
        $users = User::whereNotIn('id', $userIds)->get();

        return view('tournament-users', ['tournament' => $tournament, 'tournamentUsers' => $tournamentUsers, 'users' => $users]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'matches' => 'required|min:1',
            'duration_m' => 'required|min:1',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'max_diff_rating' => 'sometimes|required|min:0',
            'time_between_matches_m' => 'required|min:0',
		]);

        $newTournament = new Tournament([
            'name' => $request->get('name'),
            'datetime_start' => $request->get('datetime_start'),
            'datetime_end' => $request->get('datetime_end'),
            'matches' => $request->get('matches'),
            'duration_m' => $request->get('duration_m'),
            'type' => $request->get('type'),
            'allow_singles' => $request->get('allow_singles'),
            'max_diff_rating' => $request->get('max_diff_rating'),
            'time_between_matches_m' => $request->get('time_between_matches_m'),
            'created_by' => Auth::id(),
        ]);

        $newTournament->save();
        return Redirect::route('tournaments')->with('status', 'Successfully added the tournament '. $newTournament->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:tournaments',
            'name' => 'required|max:50',
            'datetime_start' => 'required|date_format:Y-m-d H:i',
            'datetime_end' => 'required|date_format:Y-m-d H:i',
            'matches' => 'required|min:1',
            'duration_m' => 'required|min:1',
            'type' => 'required', // TODO: define enum class?
            'allow_singles' => 'required',
            'max_diff_rating' => 'required|min:0',
            'time_between_matches_m' => 'required|min:0',
		]);

        $tournament = Tournament::find($request->get('id'));
        
        if ($request->has('name')) {
            $tournament->name = $request->get('name');
        }
        
        if ($request->has('datetime_start')) {
            $tournament->datetime_start = $request->get('datetime_start');
        }
        
        if ($request->has('datetime_end')) {
            $tournament->datetime_end = $request->get('datetime_end');
        }

        if ($request->has('matches')) {
            $tournament->matches = $request->get('matches');
        }

        if ($request->has('duration_m')) {
            $tournament->duration_m = $request->get('duration_m');
        }

        if ($request->has('type')) {
            $tournament->type = $request->get('type');
        }

        if ($request->has('allow_singles')) {
            $tournament->allow_singles = $request->get('allow_singles');
        }

        if ($request->has('max_diff_rating')) {
            $tournament->max_diff_rating = $request->get('max_diff_rating');
        }

        if ($request->has('time_between_matches_m')) {
            $tournament->time_between_matches_m = $request->get('time_between_matches_m');
        }

        $tournament->save();
        return Redirect::route('tournament', ['id' => $tournament->id])->with('status', 'Successfully updated the tournament details');
    }

    /**
     * Add courts to a certain tournament
     */
    public function assignCourt(Request $request) {
        $request->validate([
            'tournament' => 'required|exists:tournaments,id',
            'court' => 'required|exists:courts,id',
        ]);

        $tournamentCourt = new TournamentCourt([
            'tournament' => $request->get('tournament'),
            'court' => $request->get('court'),
        ]);

        $tournamentCourt->save();

        $court = Court::find($request->get('court'));
        return Redirect::route('tournament-courts', ['id' => $request->get('tournament')])->with('status', 'Successfully assigned '. $court->name .' to tournament');
    }

    /**
     * Add users to a certain tournament
     */
    public function assignUser(Request $request) {
        $request->validate([
            'tournament' => 'required|exists:tournaments,id',
            'user' => 'required|exists:users,id',
        ]);

        $tournamentUser = new TournamentUser([
            'tournament' => $request->get('tournament'),
            'user' => $request->get('user'),
        ]);

        $tournamentUser->save();

        $user = User::find($request->get('user'));
        return Redirect::route('tournament-users', ['id' => $request->get('tournament')])->with('status', 'Successfully assigned '. $user->name .' to tournament');
    }

    public function removeCourt(Request $request) {
        $request->validate([
            'tournament' => 'required|exists:tournaments,id',
            'court' => 'required|exists:courts,id',
        ]);

        $tournamentCourt = TournamentCourt::where('tournament', $request->get('tournament'))->where('court', $request->get('court'))->first();
        if (!$tournamentCourt) {
            throw ValidationException::withMessages(['court' => 'Court seems not to be assigned to given tournament']);
        }

        $tournamentCourt->delete();

        $court = Court::find($request->get('court'));
        return Redirect::route('tournament-courts', ['id' => $request->get('tournament')])->with('status', 'Successfully removed '. $court->name .' from the tournament');
    }

    public function removeUser(Request $request) {
        $request->validate([
            'tournament' => 'required|exists:tournaments,id',
            'user' => 'required|exists:users,id',
        ]);

        $tournamentUser = TournamentUser::where('tournament', $request->get('tournament'))->where('user', $request->get('user'))->first();
        if (!$tournamentUser) {
            throw ValidationException::withMessages(['user' => 'User seems not to be assigned to given tournament']);
        }

        $tournamentUser->delete();

        $user = User::find($request->get('user'));
        return Redirect::route('tournament-users', ['id' => $request->get('tournament')])->with('status', 'Successfully removed '. $user->name .' from the tournament');
    }

    public function delete(Request $request) {
        $request->validate([
            'id' => 'required|exists:tournaments',
        ]);

        $tournament = Tournament::find($request->id);
        $tournament->delete();

        // TODO: delete all corresponding matches as well, and assigned users and courts.

        return Redirect::route('tournaments')->with('status', 'Successfully removed the tournament '. $tournament->name);
    }
}
