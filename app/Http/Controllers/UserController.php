<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $user->load('roles.permissions', 'team');

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'email' => 'sometimes|email|unique:users,email',
            'name' => 'sometimes|string'
        ]);

        $user = Auth::user();

        if ($request->has('email')) $user->email = $request->input('email');
        if ($request->has('name')) $user->name = $request->input('name');

        $user->save();

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed|min:8',
            'new_password_confirmation' => 'required|string'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json([
                'errors' => [
                    'old_password' => ['Old password is incorrect.']
                ]
                ], 422);
        }

        $user->password = Hash::make($request->input('new_password'));

        $user->save();

        return response()->json($user);
    }
}
