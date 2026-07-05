<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $personne = $request->user();

        if (! $personne || ! in_array($personne->role, $roles, true)) {
            return response()->json([
                'message' => 'Accès refusé. Permissions insuffisantes.',
            ], 403);
        }

        if (! $personne->is_active) {
            return response()->json([
                'message' => 'Votre compte est désactivé.',
            ], 403);
        }

        return $next($request);
    }
}
