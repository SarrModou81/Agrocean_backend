<?php

// app/Http/Middleware/JwtMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }

            if (!$user->is_active) {
                return response()->json(['error' => 'Compte désactivé'], 403);
            }

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token invalide'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token expiré'], 401);
            } else {
                return response()->json(['error' => 'Token non fourni'], 401);
            }
        }

        return $next($request);
    }
}
