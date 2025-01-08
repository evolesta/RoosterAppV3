<?php

// Helper file
namespace App\Helpers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Helper
{
    // Helper function to get the user ID based on it's emailaddress of the Jwt Token
    public static function GetUser(string $token)
    {
        // decode the token
        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        $email = $decoded->user;

        // get from DB
        $user = User::where('email', $email)->first();

        return $user;
    }
}