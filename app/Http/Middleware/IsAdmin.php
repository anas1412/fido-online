<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND has is_admin = true
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // If not admin, return 403 Forbidden
        abort(403, 'Unauthorized access.');
    }
}