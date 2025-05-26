<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
      public function handle($request, Closure $next, $role)
    {
        // <-- Apakah kamu ngecek $user->role atau $user->roles ?
        if (! Auth::check() || Auth::user()->roles !== $role) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
    
}
