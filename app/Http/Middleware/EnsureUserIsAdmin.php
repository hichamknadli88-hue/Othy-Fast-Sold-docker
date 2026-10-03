<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Assumes it runs after 'auth' (i.e. $request->user() is already set —
     * an unauthenticated guest never reaches here, 'auth' already sent them
     * to the login page first).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}