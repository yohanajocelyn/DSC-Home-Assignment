<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/');
        }

        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // If staff tries to access an unauthorized route (e.g. /admin/dashboard), redirect to their own bookings
        if ($request->user()->isStaff()) {
            return redirect()->route('staff.my-bookings.index');
        }

        // If admin tries to access a role-restricted route, redirect to admin dashboard
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/');
    }
}
