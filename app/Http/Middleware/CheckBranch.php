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
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
	 */
    public function handle($request, Closure $next)
    {
    	$bIsAdmin = (bool) (explode('/', trim($request->getPathInfo(), '/'))[0] == 'admin');

    	$sRedirectTo = '/'.(($bIsAdmin) ? 'admin/select-branch' : 'branch');

		// Missing parameters
		if (!(bool) Session::get('branch_id')) {
			Flash()->error(__('Please select a branch and try again'))->important();

			// Clean session data to prevent errors
			Session::forget('branch_id');

			return redirect($sRedirectTo);
		} else {
			$oBranch = \App\Models\Branch::find(Session::get('branch_id'));

			// Branch not valid
			if (!(bool) $oBranch) {
				Flash()->error('Branch is not valid, please select a branch and try again')->important();

				// Clean session data to prevent errors
				Session::forget('branch_id');

				return redirect($sRedirectTo);
			}

			$request->attributes->set('oBranch', $oBranch);

			if ($bIsAdmin)
				$request->attributes->set('oBranches', \App\Models\Branch::where(['enable' => '1'])->orderBy('name')->get());
		}

        return $next($request);
    }
}
