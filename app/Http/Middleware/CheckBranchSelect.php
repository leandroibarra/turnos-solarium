<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBranchSelect
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
		if (Auth::user() && (Auth::user()->hasRole(['Sysadmin', 'Admin']) || (bool) Auth::user()->roles->isEmpty()))
			return $next($request);

		abort(401);
    }
}
