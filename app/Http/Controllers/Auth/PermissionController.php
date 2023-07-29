<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create_admin_permission() {
        $permission = Permission::create(['name' => 'admin']);
        if ($permission) {
            return Response::json('Successfully created permission', 200);
        } else {
            return Response::json('Failed to create permission. Perhaps the permission already exists?', 400);
        }
    }

    public function assign_admin_permission() {
        $user = Auth::User();
        if ($user->givePermissionTo('admin')) {
            return Response::json('Successfully assigned permission to '. $user->name, 200);
        } else {
            return Response::json('Failed to assign permission to '. $user->name, 400);
        }
    }

    public function revoke_admin_permission() {
        $user = Auth::User();
        if ($user->revokePermissionTo('admin')) {
            return Response::json('Successfully revoked permission of '. $user->name, 200);
        } else {
            return Response::json('Failed to revoke permission of '. $user->name, 400);
        }
    }
}
