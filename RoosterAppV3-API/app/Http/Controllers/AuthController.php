<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // login the user with ropc
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //get the first user to match the email or null
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Bad username or password!'], 400);
        }

        // Check if user is locked
        if (!$user->active) {
            return response()->json(['error' => 'Account locked.'], 403);
        }

        // validate the user password with the one in the database
        if (Hash::check($request->password, $user->password)) {

            // valid password - reset bad attempts
            $user->loginAttempts = 0;
            $user->save();

            return response()->json([
                'token' => $this->GenerateJwt($user),
                'type' => 'bearer',
                'expires' => time() + (env('JWT_TTL') * 60)
            ]);
        }
        else {
            // check if user has 3 bad login attempts
            if ($user->loginAttempts >= 3) {
                // Lock user
                $user->active = false;
                $user->save();

                return response()->json(['error' => 'Too many bad attempts. User has been locked.'], 403);
            }
            else {
                // increment bad attempts and response with a bad request
                $user->loginAttempts = $user->loginAttempts + 1;
                $user->save();

                return response()->json(['error' => 'Bad username or password!'], 400);
            }
        }
    }

    // protected function to generate a new jwt
    protected function GenerateJwt($user)
    {
        $key = env('JWT_SECRET');
        $payload = [
            'iss' => env('APP_URL'),
            'aud' => env('APP_URL'),
            'iat' => time(),
            'exp' => time() + (env('JWT_TTL') * 60),
            'user' => $user->email
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}
