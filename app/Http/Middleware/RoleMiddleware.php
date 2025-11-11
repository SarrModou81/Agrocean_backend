<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        if (!$request->user()->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est désactivé. Contactez l\'administrateur.'
            ], 403);
        }

        // Si aucun rôle n'est spécifié, on autorise tous les utilisateurs authentifiés
        if (empty($roles)) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a l'un des rôles autorisés
        if (!in_array($request->user()->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.',
                'required_roles' => $roles,
                'your_role' => $request->user()->role
            ], 403);
        }

        return $next($request);
    }
}
