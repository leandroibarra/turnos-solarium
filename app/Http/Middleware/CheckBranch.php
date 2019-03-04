<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckBranch
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
		// Missing parameters
		if (!(bool) Session::get('branch_id')) {
			Flash()->error(__('Please select a branch and try again'))->important();

			// Clean session data to prevent errors
			Session::forget('branch_id');

			return redirect('/branch');
		} else {
			$oBranch = \App\Models\Branch::find(Session::get('branch_id'));

			// Branch not valid
			if (!(bool) $oBranch) {
				Flash()->error('Branch is not valid, please select a branch and try again')->important();

				// Clean session data to prevent errors
				Session::forget('branch_id');

				return redirect('/branch');
			}

			$request->attributes->set('oBranch', $oBranch);
		}

        return $next($request);
    }
}
