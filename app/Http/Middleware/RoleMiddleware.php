<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Allow access only for users with one of the given roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!in_array($user->role, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }

        return $next($request);
    }
}
