<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

class JwtValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // validates the bearer token in the header of the request
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        // extract te token of the header value
        $token = explode(' ', $token)[1];

        // Try to decode the token, if anything isn't valid or the token is expired it will raise an exception
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        }
        catch (SignatureInvalidException $e) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }
        catch (BeforeValidException $e) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }
        catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expired.'], 401);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        // Proceed the request if the token is valid
        return $next($request);
    }
}
