<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckAppointment
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
    	if (!(bool) Session::get('date') || !(bool) Session::get('time')) {
			Flash()->error(__('Appointment could not been granted. Please, try again.'))->important();

			// Clean session data to prevent errors
			Session::forget('date');
			Session::forget('time');

			return redirect('/book');
		}

    	$oAppointment = \App\Models\Appointment::where([
			'date' => Session::get('date'),
			'time' => Session::get('time'),
			'status' => 'granted'
		])->get();

    	// Already granted appointment
    	if (
    		validateGrantedAppointments(
				Session::get('time'),
				[
					'time' => Session::get('time'),
					'amount' => $oAppointment->count()
				]
			)
		) {
			Flash()->error(__('Appointment selected is already granted.'))->important();

			// Clean session data to prevent errors
			Session::forget('date');
			Session::forget('time');

			return redirect('/book');
		}

        return $next($request);
    }
}
