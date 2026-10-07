<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur authentifié a l'un des rôles requis.
     * Accepte un ou plusieurs rôles séparés par une virgule : role:admin,client
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            return response()->json([
                'message' => 'Accès non autorisé pour votre rôle.',
            ], 403);
        }

        return $next($request);
    }
}
