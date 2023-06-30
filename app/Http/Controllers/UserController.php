<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        $users = User::all();
        return view('users', ['users' => $users]);
    }

    public function show($id) {
        $user = User::findOrFail($id);
        if ($user) {
            return Response::json($user, 200);
        } else {
            return Response::json("The given user could not be found.", 400);
        }
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users',
            'rating' => 'sometimes|required|min:0',
            'avatar' => 'sometimes|required|mimes:jpeg,png|max:4096',
            'fcm_token' => 'sometimes',
            'availability_start' => 'sometimes|required|date_format:yyyy-mm-dd H:i',
            'availability_end' => 'sometimes|required|date_format:yyyy-mm-dd H:i',
        ]);

		if ($validator->fails()) {
			return Response::json($validator->errors()->first(), 400);
		}

        $user = User::find($id);

        if ($request->has('password')) {
            if ($request->get('id') != Auth::id()) {
                return Response::json('Not authorized to update password', 400);	
            }

            $user->password = Hash::make($request->get('password'));
        }

        if ($request->has('rating')) {
            $user->rating = $request->get('rating');
        }

        if ($request->has('avatar')) {
            if ($user->avatar != null) {
                // Remove old avatar first
                Storage::delete($user->avatar);
            }

            $user->avatar = Storage::putFile('avatars', $request->file('avatar'));
        }

        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->input('fcm_token');
        }

        if ($request->has('availability_start')) {
            $user->availability_start = $request->get('availability_start');
        }

        if ($request->has('availability_end')) {
            $user->availability_end = $request->get('availability_end');
        }
    }
}
