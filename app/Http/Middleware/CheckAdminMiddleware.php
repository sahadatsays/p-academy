<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('api')->check() && !auth('api')->user()->isAdmin() && !auth('api')->user()->isRoot()) {
            if ($request->expectsJson()) {
                return response('Unauthorized Access.', 401);
            }
            return redirect('login');
        }

        return $next($request);
    }
}
