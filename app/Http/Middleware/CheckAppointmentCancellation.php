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

		if ($oAppointment->isEmpty())
			abort(404, __('Parameters are not valid'));

        return $next($request);
    }
}
