<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/** Usage : ->middleware('role:admin,responsable') — contrôle côté serveur. */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            Log::warning('Accès refusé par rôle', [
                'user_id' => $user?->id,
                'role' => $user?->role,
                'url' => $request->fullUrl(),
            ]);
            abort(403, 'Action non autorisée pour votre profil.');
        }

        return $next($request);
    }
}
