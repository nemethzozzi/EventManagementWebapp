<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Bejövő kérés kezelése a megadott szerepkörök alapján
     *
     * @param  string  ...$roles
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        if (! auth('api')->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = auth('api')->user();

        if (! in_array($user->role, $roles, true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
