<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\UserOrganizationalAssignment;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Verification of super user happens at middleware level
        $organizations = Organization::all();
        return view('superuser.organizations', ['organizations' => $organizations]);
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);

        // TODO(PATBRO): verify user is owner of the organization, or assigned to it? So multiple users could be owner of an organization (with separate access levels perhaps)
        if (!Auth::user()->can('superuser') && Auth::id() != $organization->owner_user_id) {
            return "User is not allowed to access this organization";
        }

        return view('admin.organization', ['organization' => $organization]);
    }

    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|max:70',
            'location' => 'required|max:70',
        ]);

        $newOrganization = new Organization([
            'name' => $request->get("name"),
            'location' => $request->get("location"),
            'owner_user_id' => Auth::id(), // TODO(PATBRO): provide ability to specify user
        ]);

        $organization = $newOrganization->save();

        $organization = Organization::find($organization->id);
        return Redirect::route('organization', ['organization' => $organization]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:organizations',
            'name' => 'sometimes|required|max:70',
            'location' => 'sometimes|required|max:70',
        ]);

        $organization = Organization::findOrFail($request->get('id'));

        if ($organization->owner_user_id != Auth::id()) {
            return "User is not allowed to access this organization";
        }

        if ($request->has('name')) {
            $organization->name = $request->get('name');
        }

        if ($request->has('location')) {
            $organization->location = $request->get('location');
        }

        $organization->save();
        return Redirect::route('organization', ['organization' => $organization]);
    }

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
}
