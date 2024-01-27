<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Http\Controllers\PlannerWrapperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CourtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($court_id) {
        $court = Court::findOrFail($court_id);

        $tournament = Tournament::find($court->tournament_id);
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        return view('admin.court', ['court' => $court]);
    }

    public function store(Request $request) {
        $request->validate([
			'name' => 'required|max:30',
            'tournament_id' => 'required|exists:tournaments,id',
		]);

        $tournament = Tournament::find($request->get('tournament_id'));
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $newCourt = new Court([
            'name' => $request->get('name'),
            'tournament_id' => $request->get('tournament_id'),
            'created_by' => Auth::id(),
        ]);

        $newCourt->save();

        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_courts_rounds = true;
        $tournament->save();

        return Redirect::route('courts-rounds', ['tournament_id' => $request->get('tournament_id')])->with('status', 'Successfully added the court '. $newCourt->name);
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|exists:courts',
            'name' => 'sometimes|required|max:30',
        ]);

        $court = Court::find($request->get('id'));

        $tournament = Tournament::find($court->tournament_id);
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }
        
        if ($request->has('name')) {
            $court->name = $request->get('name');
        }

        $court->save();
        
        return Redirect::route('courts-rounds', ['tournament_id' => $court->tournament_id])
            ->with('status', 'Successfully updated court details for '. $court->name);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:courts',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);	
		}

        $court = Court::find($request->get('id'));
        $tournament = Tournament::find($court->tournament_id);
        $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $tournament->owner_organization_id)->get();
        if (count($organizations) == 0 && !Auth::user()->can('superuser')) {
            return redirect('home')->with('error', 'You are not allowed to access this page');
        }

        $court->delete();
        // Schedule becomes invalid if a court is removed
        $schedule = Schedule::where('tournament_id', $request->get('tournament_id'))
            ->where('court_id', $request->get('id'))
            ->delete();

        $tournament = Tournament::find($request->get('tournament_id'));
        $tournament->change_to_courts_rounds = true;
        $tournament->save();

        return Redirect::route('courts-rounds', ['tournament_id' => $court->tournament_id])
            ->with('status', 'Successfully deleted court '. $court->name);
    }
}
