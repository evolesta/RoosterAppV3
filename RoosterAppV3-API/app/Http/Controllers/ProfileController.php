<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Gets the current user profile
    public function show(Request $request)
    {
        $token = $request->header("Authorization");
        return response()->json(Helper::GetUser($token));
    }

    // Stores updated profile
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'workHours' => 'required'
        ]);
        
        $token = $request->header("Authorization");
        $user = Helper::GetUser($token);

        $user->name = $request->name;
        $user->workHours = $request->workHours;
        $user->save();

        return response()->json($user);
    }

    // Changes password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required',
            'newPassword2' => 'required',
        ]);

        $token = $request->header("Authorization");
        $user = Helper::GetUser($token);

        // check if old password is valid 
        if (Hash::check($request->oldPassword, $user->password)) {
            // password is valid
            // check if passwords match
            if ($request->newPassword != $request->newPassword2) {
                return response()->json(['error' => 'No valid request.'], 400);
            }

            // Hash new password and change it to database
            $user->password = Hash::make($request->newPassword);
            $user->save();

            return response()->jsom(['Result' => 'OK']);
        }
        else {
            // Invalid current password
            return response()->json(['error' => 'No valid request.'], 400);
        }
    }
}
