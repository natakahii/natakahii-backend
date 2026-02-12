<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminLevel
{
    /**
     * Handle an incoming request.
     *
     * Checks that the authenticated user is an admin and has one of the
     * specified admin levels. Usage: middleware('admin.level:normal_admin,super_admin').
     *
     * Admin level mapping:
     *  - normal_admin => user has the "admin" role
     *  - super_admin  => user has the "admin" role AND email matches the super admin
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$levels): Response
    {
        $user = $request->user('api');

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! $user->hasRole('admin')) {
            return response()->json([
                'message' => 'Forbidden. Admin access required.',
            ], 403);
        }

        if (in_array('super_admin', $levels) && ! in_array('normal_admin', $levels)) {
            if (! $user->isSuperAdmin()) {
                return response()->json([
                    'message' => 'Forbidden. Super admin access required.',
                ], 403);
            }
        }

        return $next($request);
    }
}
