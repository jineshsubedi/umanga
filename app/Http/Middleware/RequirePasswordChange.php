<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
            if (is_null(auth()->user()->password_changed_at)) {
                if (!$request->routeIs('password.force-change', 'password.force-change.store', 'logout')) {
                    return redirect()->route('password.force-change');
                }
            }
        }

        return $next($request);
    }
}
