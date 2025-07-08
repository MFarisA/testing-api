<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->hasRole('super-admin')) {
            return response()->json([
                'message' => 'Forbidden. Super admin access required.',
                'error' => 'insufficient_privileges'
            ], 403);
        }
        
        return $next($request);
    }
}
