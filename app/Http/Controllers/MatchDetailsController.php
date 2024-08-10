<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\MatchDetails;
use App\Models\Player;
use App\Models\Round;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Models\UserOrganizationalAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MatchDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $matches = MatchDetails::all();
        $past_matches = []; // TODO: provide past matches

        return view('matches', ['upcoming_matches' => $matches, 'past_matches' => $past_matches]);
    }

    public function show($id) {
        $match = DB::select("SELECT 
                rounds.starttime as 'time',
                courts.name as 'court',
                user1a.name as `player1a`,
                user1a.id as `player1a_id`,
		user1a.avatar as `player1a_avatar`,
		user1a.rating as `player1a_rating`,
                user1b.name as `player1b`,
                user1b.id as `player1b_id`,
                user1b.avatar as `player1b_avatar`,
		user1b.rating as `player1b_rating`,
                user2a.name as `player2a`,
                user2a.id as `player2a_id`,
                user2a.avatar as `player2a_avatar`,
		user2a.rating as `player2a_rating`,
                user2b.name as `player2b`,
                user2b.id as `player2b_id`,
                user2b.avatar as `player2b_avatar`,
		user2b.rating as `player2b_rating`,
                matches.id,
                matches.score1,
                matches.score2,
                tournaments.name as `tournament_name`,
                tournaments.id as `tournament_id`
            FROM `schedules`
                INNER JOIN `tournaments` ON schedules.tournament_id = tournaments.id
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
            WHERE schedules.match_id = ?", [$id]);

        $tournament = Tournament::find($match[0]->tournament_id);
        $organizations = UserOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $match[0]->time = date('H:i', strtotime($match[0]->time));
        
        return view('match', ['match' => $match[0]]);
    }
    
    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:matches',
            'score1' => 'required',
            'score2' => 'required',
        ]);

        $match = MatchDetails::find($request->get('id'));

        $player1a = Player::find($match->player1a_id);
        $player1b = Player::find($match->player1b_id);
        $player2a = Player::find($match->player2a_id);
        $player2b = Player::find($match->player2b_id);

        // Prevent adding score before start of tournament
        $tournament = Tournament::findOrFail($match->tournament_id);
        if (date('Y-m-d H:i:s') < $tournament->datetime_start) {
            return back()->with('error', 'The tournament did not start yet');
        }

        $isUserAdmin = Tournament::where('tournaments.id', $tournament->id)
            ->leftJoin('admins_organizational_assignment', 'admins_organizational_assignment.id', '=', 'tournaments.owner_organization_id')
            ->where('admins_organizational_assignment.user_id', Auth::id())
            ->first();
        
        if (!$isUserAdmin && !Auth::user()->can('superuser')) {
            if ($player1a->user_id != Auth::id() && $player1b->user_id != Auth::id() && 
                $player2a->user_id != Auth::id() && $player2b->user_id != Auth::id()) {
                return back()->withInput()->with('error', 'You are not allowed to save the score for this match');
            }
            
            if ($match->score1 != null || $match->score2 != null) {
                return back()->withInput()->with('error', 'Score has been set already for this match');
            }
        }

        if ($request->get('score1') == "" || $request->get('score1') < 0) {
            $match->score1 = 0;
        } elseif ($match->score1 != null) {
            // Update score
            $player1a->points += ($request->get('score1') - $match->score1);
            $player1b->points += ($request->get('score1') - $match->score1);

            $match->score1 = $request->get('score1');
        } else {
            // Add score
            $player1a->points += $request->get('score1');
            $player1b->points += $request->get('score1');

            $match->score1 = $request->get('score1');
        }

        if ($request->get('score2') == "" || $request->get('score2') < 0) {
            $match->score2 = 0;
        } elseif ($match->score2 != null) {
            // Update score
            $player2a->points += ($request->get('score2') - $match->score2);
            $player2b->points += ($request->get('score2') - $match->score2);

            $match->score2 = $request->get('score2');
        } else {
            // Add score
            $player2a->points += $request->get('score2');
            $player2b->points += $request->get('score2');

            $match->score2 = $request->get('score2');
        }

        $match->save();
        $player1a->save();
        $player1b->save();
        $player2a->save();
        $player2b->save();

        return back()->withInput();
    }

    public function create_match($tournament_id, $slot_id) {
        $schedule = Schedule::findOrFail($slot_id);

        $tournament = Tournament::findOrFail($schedule->tournament_id);

        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $round = Round::findOrFail($schedule->round_id);
        $time = date('H:i', strtotime($round->starttime));

        $court = Court::findOrFail($schedule->court_id);

        $tournament_players = Player::where('tournament_id', $tournament->id)
            ->select('players.*', 'users.id as user_id', 'users.name')
            ->leftJoin('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();

        return view('admin.match-create', [
            'tournament' => $tournament,
            'slot_id' => $slot_id,
            'time' => $time, 
            'court' => $court->name, 
            'tournament_players' => $tournament_players
        ]);
    }

    public function store_match(Request $request) {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'slot_id' => 'required|exists:schedules,id',
            'player1a_id' => 'required|exists:players,id',
            'player1b_id' => 'required|exists:players,id',
            'player2a_id' => 'required|exists:players,id',
            'player2b_id' => 'required|exists:players,id',
        ]);

        $tournament = Tournament::find($request->get('tournament_id'));
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $match = new MatchDetails([
            'tournament_id' => $request->get('tournament_id'),
            'player1a_id' => $request->get('player1a_id'),
            'player1b_id' => $request->get('player1b_id'),
            'player2a_id' => $request->get('player2a_id'),
            'player2b_id' => $request->get('player2b_id'),
        ]);

        $match->save();

        Schedule::where('id', $request->get('slot_id'))->update(['match_id' => $match->id]);
        
        return redirect()->to(route('tournament', ['tournament_id' => $request->get('tournament_id')]) .'?showround=all#slot'. $request->get('slot_id'))->with('status', 'Manually filled slot');
    }

    public function edit_match($id) {
        $match = DB::select("SELECT 
                rounds.starttime as 'time',
                courts.name as 'court',
                user1a.name as `player1a`,
                user1a.id as `player1a_id`,
                user1a.avatar as `player1a_avatar`,
                user1b.name as `player1b`,
                user1b.id as `player1b_id`,
                user1b.avatar as `player1b_avatar`,
                user2a.name as `player2a`,
                user2a.id as `player2a_id`,
                user2a.avatar as `player2a_avatar`,
                user2b.name as `player2b`,
                user2b.id as `player2b_id`,
                user2b.avatar as `player2b_avatar`,
                matches.id,
                matches.score1,
                matches.score2,
                tournaments.name as `tournament_name`,
                tournaments.id as `tournament_id`
            FROM `schedules`
                INNER JOIN `tournaments` ON schedules.tournament_id = tournaments.id
                INNER JOIN `rounds` ON schedules.round_id = rounds.id
                INNER JOIN `courts` ON schedules.court_id = courts.id
                INNER JOIN `matches` ON schedules.match_id = matches.id
                LEFT JOIN `players` as player1a ON matches.player1a_id = player1a.id
                LEFT JOIN `players` as player1b ON matches.player1b_id = player1b.id
                LEFT JOIN `players` as player2a ON matches.player2a_id = player2a.id
                LEFT JOIN `players` as player2b ON matches.player2b_id = player2b.id
                LEFT JOIN `users` as user1a ON player1a.user_id = user1a.id
                LEFT JOIN `users` as user1b ON player1b.user_id = user1b.id
                LEFT JOIN `users` as user2a ON player2a.user_id = user2a.id
                LEFT JOIN `users` as user2b ON player2b.user_id = user2b.id
            WHERE schedules.match_id = ?", [$id]);

        $tournament = Tournament::find($match[0]->tournament_id);
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $match[0]->time = date('H:i', strtotime($match[0]->time));

        $tournament_players = Player::where('tournament_id', $match[0]->tournament_id)
            ->select('players.*', 'users.id as user_id', 'users.name')
            ->leftJoin('users', 'users.id', '=', 'players.user_id')
            ->orderBy('users.name')
            ->get();

        return view('admin.match-edit', ['match' => $match[0], 'tournament_players' => $tournament_players]);
    }

    public function update_match(Request $request) {
        $request->validate([
            'id' => 'required|exists:matches',
            'player1a_id' => 'required|exists:players,id',
            'player1b_id' => 'required|exists:players,id',
            'player2a_id' => 'required|exists:players,id',
            'player2b_id' => 'required|exists:players,id',
        ]);

        $schedule = Schedule::where('match_id', $request->get('id'))->first();

        $tournament = Tournament::find($schedule->tournament_id);
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to perform this action');
        }

        $match = MatchDetails::find($request->get('id'));
        $match->player1a_id = $request->get('player1a_id');
        $match->player1b_id = $request->get('player1b_id');
        $match->player2a_id = $request->get('player2a_id');
        $match->player2b_id = $request->get('player2b_id');
        $match->save();

        return redirect()->to(route('tournament', ['tournament_id' => $schedule->tournament_id]) .'?showround=all#slot'. $schedule->id)->with('status', 'Manually updated slot');
    }
}
