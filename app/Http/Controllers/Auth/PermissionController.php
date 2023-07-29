<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
            return Response::json('Failed to assign permission to '. $user->name, 200);
        }
    }
}
