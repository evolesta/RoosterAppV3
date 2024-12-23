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
            return response()->json(['error' => 'Bad username or password!'], 401);
        }

        // validate the user password with the one in the database
        if (Hash::check($request->password, $user->password)) {
            // valid 
            return response()->json([
                'token' => $this->GenerateJwt($user),
                'type' => 'bearer',
                'expires' => time() + (env('JWT_TTL') * 60)
            ]);
        }
        else {
            return response()->json(['error' => 'Bad username or password!'], 401);
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
