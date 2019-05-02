<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAppointmentCancellation
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
		$oAppointment = \App\Models\Appointment::where([
			'branch_id' => current($request->attributes)['oBranch']->id,
			'user_id' => Auth::user()->id,
			'id' => $request->route('id')
		])->get();

		if (!(bool) $oAppointment)
			abort(403);

        return $next($request);
    }
}
