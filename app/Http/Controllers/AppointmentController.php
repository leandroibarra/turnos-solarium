<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Mail\AppointmentConfirmed;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
	public function index($piYear, $piMonth, $piDay, Request $request) {
		$oToday = new Date();

		$oRequestDateTime = new Date("{$piYear}-{$piMonth}-{$piDay}");

		$aWorkingHoursPerDay = config('app.working_hours_per_day')[$oRequestDateTime->format('w')];

		$aMorning = $aAfternoon = $aNight = [];

		$aHours = range($aWorkingHoursPerDay[0], $aWorkingHoursPerDay[1], 1);

		foreach ($aHours as $iHour) {
			if ($iHour < 12)
				$aMorning[] = $iHour;
			else if ($iHour>=12 && $iHour<17)
				$aAfternoon[] = $iHour;
			else
				$aNight[] = $iHour;
		}

		$oAppointment = new \App\Appointment();

		return view('partials.appointment')->with([
			'oToday' => $oToday,
			'oRequestDateTime' => $oRequestDateTime,
			'aGrantedAppointments' => $oAppointment->getGrantedByDate("{$piYear}-{$piMonth}-{$piDay}")->toArray(),
			'aMorning' => $aMorning,
			'aAfternoon' => $aAfternoon,
			'aNight' => $aNight
		]);
	}

	/*
	public function create(Request $request) {
//		Flash()->error(__('Appointment is already granted.'))->important();
//		Flash()->error(__('Appointment could not been granted. Please, try again.'))->important();
//		return redirect('/book');
//    	dd($request->input());

		$oDateTime = new Date("{$request->input('date')} {$request->input('time')}");

		return view('confirm')->with([
			'date' => $request->input('date'),
			'time' => $request->input('time'),
			'oDateTime' => $oDateTime
		]);
	}
	*/

	public function set(Request $request) {
		Session::put('date', $request->input('date'));
		Session::put('time', $request->input('time'));
	}

	public function store(Request $request) {
		$this->validate(
			$request,
			[
				'name' => 'required',
				'phone' => [
					'required',
					function ($attribute, $value, $fail) {
						$number = preg_replace( '/\D+/', '', $value);

						if (preg_match('/^(?:(?:00)?549?)?0?(?:11|[2368]\d)(?:(?=\d{0,2}15)\d{2})??\d{8}$/D', $number) != 1)
							$fail(__('The :attribute field is not valid.'));
					}
				]
			],
			[],
			[
				'name' => strtolower(__('Name')),
				'phone' => strtolower(__('Phone Number'))
			]
		);

		// Complete rest of data
		$request->request->add([
			'user_id' => Auth::user()->id,
			'date' => Session::get('date'),
			'time' => Session::get('time')
		]);

		$oAppointment = new Appointment($request->all());
		$oAppointment->save();

		$oDateTime = new Date(Session::get('date').' '.Session::get('time'));

		$oContent = new \stdClass();
		$oContent->sName = $request->input('name');
		$oContent->sDate = $oDateTime->format('j').' '.__('of').' '.$oDateTime->format('F');
		$oContent->sTime = $oDateTime->format('H:i a');

		// Send confirmation email
		Mail::to(Auth::user()->email)->send(new AppointmentConfirmed($oContent));

		// Clean session data to prevent errors
		Session::forget('date');
		Session::forget('time');

		Flash()->success(__('Appointment has been granted successfully. We sent you an email with appointment data.'))->important();

		return redirect('/book');
	}
}
