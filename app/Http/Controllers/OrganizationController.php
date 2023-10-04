<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\AdminOrganizationalAssignment;
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
        if ($user->can('superuser')) {
            $organizations = Organization::all();
        } else {
            $adminAssignedOrganizations = AdminOrganizationalAssignment::where('user_id', Auth::id())
                ->join('organizations', 'organizations.id', '=', 'admins_organizational_assignment.organization_id');
        }
        
        return view('superuser.organizations', ['organizations' => $organizations]);
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);

        if (!Auth::user()->can('superuser') && !AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $organization->id)) {
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

        // Create new organization
        $newOrganization = new Organization([
            'name' => $request->get("name"),
            'location' => $request->get("location"),
        ]);

        $organization = $newOrganization->save();

        if (!Auth::user()->can('superuser')) {
            // Assign current user as admin to organization
            $newAdminOrganizationalAssignment = new AdminOrganizationalAssignment([
                'organization_id' => $organization->id,
                'user_id' => Auth::id(),
            ]);

            $newAdminOrganizationalAssignment->save();
        }

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
            'user_id' => 'required|exists:users,id',
        ]);

        if (!Auth::user()->can('superuser') && !AdminOrganizationalAssignment::where('user_id', Auth::id())->where('organization_id', $request->get('organization_id'))) {
            return "User is not allowed to perform this action";
        }

        $newAdminOrganizationalAssignment = new AdminOrganizationalAssignment([
            'organization_id' => $request->get('organization_id'),
            'user_id' => $request->get('user_id'),
        ]);

        $newAdminOrganizationalAssignment->save();

        return Redirect::route('view-organization', ['id' => $request->get('organization_id')])
                ->with('status', 'Successfully assigned assigned user to organization as adminstrator');
    }
}
