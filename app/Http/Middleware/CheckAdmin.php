<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	if (!Auth::user() || !in_array(current(Auth::user()->getRoles()), ['sysadmin', 'admin']))
			abort(401);

        return $next($request);
    }
}
