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

    public function create_permissions() {
        $permission_admin = true;
        if (count(Permission::findByName('admin')->get()) == 0) {
            $permission_admin = Permission::create(['name' => 'admin']);
        }

        $permission_superuser = true;
        if (count(Permission::findByName('superuser')->get()) == 0) {
            $permission_superuser = Permission::create(['name' => 'superuser']);
        }

        if ($permission_admin && $permission_superuser) {
            return Response::json('Successfully created permissions', 200);
        } else {
            return Response::json('Failed to create permissions. Perhaps the permission already exists?', 400);
        }
    }

    public function assign_admin_permission() {
        $user = Auth::User();
        if ($user->givePermissionTo('admin')) {
            return Response::json('Successfully assigned admin permission to '. $user->name, 200);
        } else {
            return Response::json('Failed to assign permission to '. $user->name, 400);
        }
    }

    public function assign_superuser_permission() {
        $user = Auth::User();
        if ($user->givePermissionTo('superuser')) {
            return Response::json('Successfully assigned superuser permission to '. $user->name, 200);
        } else {
            return Response::json('Failed to assign permission to '. $user->name, 400);
        }
    }

    public function revoke_permissions() {
        $user = Auth::User();
        if ($user->revokePermissionTo('admin') && $user->revokePermissionTo('superuser')) {
            return Response::json('Successfully revoked permission of '. $user->name, 200);
        } else {
            return Response::json('Failed to revoke permission of '. $user->name, 400);
        }
    }
}
