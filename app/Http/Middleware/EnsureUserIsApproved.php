<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $user->loadMissing(['role:id,role_name', 'status:id,status_name']);

        if ($user->role?->role_name === 'Administrator') {
            return $next($request);
        }

        $statusName = $user->status?->status_name;

        if ($statusName === 'Pending Approval' && $user->approved_by === null) {
            if (! $request->routeIs('account.pending')) {
                return redirect()->route('account.pending');
            }

            return $next($request);
        }

        if ($statusName === 'Inactive' && $user->rejected_at !== null) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account request was rejected by the administrator.']);
        }

        if ($statusName !== 'Active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is not yet allowed to access the system.']);
        }

        return $next($request);
    }
}
