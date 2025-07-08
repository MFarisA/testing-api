<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermissionOrSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Allow super admin to access everything
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Check specific permission for non-super-admin users
        $routeName = $request->route()?->getName();
        if ($routeName && $user->can($routeName)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized.',
            'error' => 'insufficient_permissions'
        ], 403);
    }
}