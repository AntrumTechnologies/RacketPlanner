<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\AdminOrganizationalAssignment;
use App\Models\UserOrganizationalAssignment;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->can('superuser')) {
            $organizations = Organization::all();
        } else {
            $organizations = AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->join('organizations', 'organizations.id', '=', 'admins_organizational_assignment.organization_id')->get();

            if ($organizations->count() == 0) {
                $organizations = UserOrganizationalAssignment::where('user_id', Auth::id())
                    ->join('organizations', 'organizations.id', '=', 'users_organizational_assignment.organization_id')->get();
            }
        }
        
        return view('organizations', ['organizations' => $organizations]);
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);

        $is_user_admin = false;
        $isUserAdmin = AdminOrganizationalAssignment::where('user_id', Auth::id())
            ->where('organization_id', $organization->id)
            ->first();
        if ($isUserAdmin || Auth::user()->can('superuser')) {
            $is_user_admin = true;
        } else {
            $is_user = UserOrganizationalAssignment::where('user_id', Auth::id())
                ->where('organization_id', $organization->id)
                ->first();

            if (!$is_user) {
                return "You are not allowed to access this organization";
            }
        }

        $tournaments = Tournament::where('owner_organization_id', $id)
            ->leftJoin('organizations', 'organizations.id', '=', 'tournaments.owner_organization_id')
            ->select('tournaments.*', 'organizations.name as organizer', 'organizations.id as organization_id')
            ->get();

        foreach ($tournaments as $tournament) {
            $tournament->rounds = count(Round::where('tournament_id', $tournament->id)->get());

            // Check whether user is enrolled in this tournament or not
            $tournament->is_enrolled = false;
            if (Player::where('tournament_id', $tournament->id)->where('user_id', Auth::id())->count() > 0) {
                $tournament->is_enrolled = true;
            }

            // Prepare number of players in tournament
            $no_players = Player::where('tournament_id', $tournament->id)->count();

            $tournament->can_enroll = true;
            if ((!empty($tournament->enroll_until) && date('Y-m-d H:i') > $tournament->enroll_until) ||
                (!empty($tournament->max_players) && $tournament->max_players != 0 && $no_players >= $tournament->max_players)) {
                $tournament->can_enroll = false;
            }

            // Remove seconds from datetime fields, these are not relevant, but are added due to the PHPMyAdmin config
            $tournament->datetime_start = date('Y-m-d H:i', strtotime($tournament->datetime_start));
            $tournament->datetime_end = date('Y-m-d H:i', strtotime($tournament->datetime_end));
            if (!empty($tournament->enroll_until)) {
                $tournament->enroll_until = date('Y-m-d H:i', strtotime($tournament->enroll_until));
            }

            $tournament->score = 0;
            $points = Player::where('user_id', Auth::id())->where('tournament_id', $tournament->id)->select('points')->get();
            foreach ($points as $point) {
                $tournament->score += $point->points;
            }
        }

        return view('organization', ['organization' => $organization, 'tournaments' => $tournaments, 'is_user_admin' => $is_user_admin]);
    }

    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|max:70',
            'location' => 'required|max:70',
        ]);

        // Create new organization
        $newOrganization = new Organization([
            'name' => $request->get('name'),
            'location' => $request->get('location'),
        ]);

        $newOrganization->save();

        return Redirect::route('organization', ['id' => $newOrganization->id]);
    }

    public function edit($organization_id) {
        $organization = Organization::findOrFail($organization_id);

        $isUserAdmin = AdminOrganizationalAssignment::where('user_id', Auth::id())
            ->where('organization_id', $organization->id)
            ->first();
        if (!$isUserAdmin && !Auth::user()->can('superuser')) {
            return "You are not authorized to edit this organization";
        }

        $admins = AdminOrganizationalAssignment::where('organization_id', $organization_id)
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->select('users.name', 'users.id')
            ->orderBy('users.name', 'asc')
            ->get();

        $users = UserOrganizationalAssignment::where('organization_id', $organization_id)
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->select('users_organizational_assignment.id as initid', 'users.name', 'users.id', 'users.rating')
            ->orderBy('users.name', 'asc')
            ->get();

        return view('admin/organization-edit', ['organization' => $organization, 'admins' => $admins, 'users' => $users]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:organizations',
            'name' => 'sometimes|required|max:70',
            'location' => 'sometimes|required|max:70',
        ]);

        $organization = Organization::findOrFail($request->get('id'));

        if (!Auth::user()->can('superuser') && !AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $request->get('id'))) {
            return "User is not allowed to perform this action";
        }

        if ($request->has('name')) {
            $organization->name = $request->get('name');
        }

        if ($request->has('location')) {
            $organization->location = $request->get('location');
        }

        $organization->save();
        return Redirect::route('edit-organization', ['id' => $request->get('id')])
                ->with('status', 'Successfully updated organization details');
    }

    /**
     * Assigns a user, as a normal user (i.e. member) to the organization
     */
    public function assign_user_to_organization(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $alreadyPresent = UserOrganizationalAssignment::where('organization_id', $request->get('organization_id'))
            ->where('user_id', $request->get('user_id'))->firstOrFail();
        if ($alreadyPresent->count() > 0) {
            return Redirect::route('view-organization', ['id' => $request->get('organization_id')])
                ->with('status', 'User is already assigned to this organization');
        }

        $newUserOrganizationalAssignment = new UserOrganizationalAssignment([
            'organization_id' => $request->get('organization_id'),
            'user_id' => $request->get('user_id'),
        ]);

        $newUserOrganizationalAssignment->save();
        return Redirect::route('view-organization', ['id' => $request->get('organization_id')])
                ->with('status', 'Successfully assigned assigned user to organization');
    }

    /**
     * Assigns a user as an administrator to the organization
     */
    public function assign_admin_to_organization(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'email' => 'required|exists:users,email',
        ]);

        if (!Auth::user()->can('superuser') && !AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $request->get('organization_id'))) {
            return Redirect::route('edit-organization', ['id' => $request->get('organization_id')])
                ->with('error', 'User is not allowed to perform this action');
        }

        $user = User::where('email', $request->get('email'))->first();

        if (AdminOrganizationalAssignment::where('user_id', $user->id)->where('organization_id', $request->get('organization_id'))->count() > 0) {
            return Redirect::route('edit-organization', ['id' => $request->get('organization_id')])
                ->with('status', $user->name .' is already assigned to this organization as adminstrator');
        }

        $newAdminOrganizationalAssignment = new AdminOrganizationalAssignment([
            'organization_id' => $request->get('organization_id'),
            'user_id' => $user->id,
        ]);

        $newAdminOrganizationalAssignment->save();

        if (UserOrganizationalAssignment::where('organization_id', $request->get('organization_id'))->where('user_id', $user->id)->count() == 0) {
            $newUserOrganizationalAssignment = new UserOrganizationalAssignment([
                'organization_id' => $request->get('organization_id'),
                'user_id' => $user->id,
            ]);

            $newUserOrganizationalAssignment->save();
        }

        return Redirect::route('edit-organization', ['id' => $request->get('organization_id')])
            ->with('status', 'Successfully assigned '. $user->name .' to organization as adminstrator');
    }

    public function remove_admin_from_organization(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'id' => 'required|exists:users,id',
            'name' => 'required',
        ]);

        if (!Auth::user()->can('superuser') && !AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $request->get('organization_id'))) {
            return "User is not allowed to perform this action";
        }

        $admin = AdminOrganizationalAssignment::where('organization_id', $request->get('organization_id'))
            ->where('user_id', $request->get('id'))
            ->delete();

        return Redirect::route('edit-organization', ['id' => $request->get('organization_id')])
            ->with('status', 'Successfully removed '. $request->get('name') .' as an administrator.');
    }

    public function remove_user_from_organization(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'id' => 'required|exists:users,id',
            'name' => 'required',
        ]);

        if (!Auth::user()->can('superuser') && !AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $request->get('organization_id'))) {
            return "User is not allowed to perform this action";
        }

        $user = UserOrganizationalAssignment::where('organization_id', $request->get('organization_id'))
            ->where('user_id', $request->get('id'))
            ->delete();

        if (AdminOrganizationalAssignment::where('organization_id', $request->get('organization_id'))->where('user_id', $request->get('id'))->count() > 0) {
            $admin = AdminOrganizationalAssignment::where('organization_id', $request->get('organization_id'))
                ->where('user_id', $request->get('id'))
                ->delete();
        }

        return Redirect::route('edit-organization', ['id' => $request->get('organization_id')])
            ->with('status', 'Successfully removed '. $request->get('name') .' as a user.');
    }
}
