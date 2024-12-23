<?php

namespace App\Http\Middleware;

use Closure;
use \stdClass;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class RoleGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $token = explode(' ', $token)[1]; 

        $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        
        $user = User::where('email', $decoded->user)->first();

        if ($user->role != 1) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }
        
        return $next($request);
    }
}
