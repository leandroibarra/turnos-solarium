<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Mail\AppointmentConfirmed;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
	public function index($piYear, $piMonth, $piDay, Request $request) {
		$aSystemParameters = \App\Models\SystemParameter::find(1)->toArray();

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

		$oAppointment = new Appointment();

		$oException = new  \App\Models\Exception();

		return view('web.partials.appointment')->with([
			'iAppointmentMinutes' => $aSystemParameters['appointment_minutes'],
			'oToday' => $oToday,
			'oRequestDateTime' => $oRequestDateTime,
			'aAppointmentToExclude' => ((bool) $request->headers->get('appointment-id')) ? $oAppointment::find($request->headers->get('appointment-id'))->toArray() : [],
			'aGrantedAppointments' => $oAppointment->getGrantedByDate("{$piYear}-{$piMonth}-{$piDay}")->toArray(),
			'aMorning' => $aMorning,
			'aAfternoon' => $aAfternoon,
			'aNight' => $aNight,
			'aExceptions' => $oException->getEnabledByDate($oRequestDateTime->format('Y-m-d'))->toArray()
		]);
	}

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

		$oException = new \App\Models\Exception();

		if ((bool) $oException->getEnabledByDate(Session::get('date').' '.Session::get('time'))->toArray()) {
			// Clean session data to prevent errors
			Session::forget('date');
			Session::forget('time');

			Flash()->error(__('Appointment could not been granted. Please, try again.'))->important();

			return redirect('/book');
		}

		// Complete rest of data
		$request->request->add([
			'user_id' => Auth::user()->id,
			'date' => Session::get('date'),
			'time' => Session::get('time')
		]);

		$oAppointment = new Appointment($request->all());
		$oAppointment->save();

		$this->sendConfirmationEmail(
			Auth::user()->email,
			$request->input('name'),
			Session::get('date'),
			Session::get('time')
		);

		// Clean session data to prevent errors
		Session::forget('date');
		Session::forget('time');

		Flash()->success(__('Appointment has been granted successfully. We sent you an email with appointment data.'))->important();

		return redirect('/book');
	}

	public function list() {
		$oAppointment = new Appointment();

		return view('admin.appointment')->with([
			'aGrantedAppointments' => $oAppointment->getNextGranted()
		]);
	}

	public function cancel(Request $request, $id) {
		if ($request->ajax()) {
			$aAppointment = Appointment::find($id);

			if ($aAppointment->status == 'granted') {
				Appointment::whereId($id)->update([
					'status' => 'cancelled',
					'updated_at' => date('Y-m-d H:i:s')
				]);

				$aResponse = [
					'status' => 'success',
					'message' => __('The appointment has been cancelled successfully.')
				];
			} else {
				$aResponse = [
					'status' => 'error',
					'message' => __('The appointment could not be cancelled. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}

	public function reschedule($id) {
		$aAppointment = $this->validateStatus($id, 'granted');

		if (!(bool) $aAppointment) {
			Flash()->error(__('This appointment already has been cancelled or rescheduled.'))->important();

			return redirect('/admin/appointments');
		}

		return view('admin.appointment-reschedule')->with([
			'aAppointment' => $aAppointment
		]);
	}

	public function update(Request $request, $id) {
		$aAppointment = $this->validateStatus($id, 'granted');

		if (!(bool) $aAppointment) {
			Flash()->error(__('This appointment already has been cancelled or rescheduled.'))->important();

			return redirect('/admin/appointments');
		}

		$oException = new \App\Models\Exception();

		if ((bool) $oException->getEnabledByDate($request->input('date').' '.$request->input('time'))->toArray()) {
			Flash()->error(__('Appointment could not been granted. Please, try again.'))->important();

			return redirect('/admin/appointments');
		}

		if (
			validateGrantedAppointments(
				$request->input('time'),
				[
					'time' => $request->input('time'),
					'amount' => Appointment::where([
						'date' => $request->input('date'),
						'time' => $request->input('time'),
						'status' => 'granted'
					])->get()->count()
				]
			)
		) {
			Flash()->error(__('Appointment selected is already granted.'))->important();

			return redirect('/admin/appointments');
		}

		Appointment::whereId($aAppointment['id'])->update([
			'status' => 'rescheduled',
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$oAppointment = new Appointment([
			'parent_appointment_id' => $aAppointment['id'],
			'user_id' => $aAppointment['user_id'],
			'date' => $request->input('date'),
			'time' => $request->input('time'),
			'name' => $aAppointment['name'],
			'phone' => $aAppointment['phone'],
			'comment' => $aAppointment['comment'],
			'status' => 'granted',
			'created_at' => date('Y-m-d H:i:s')
		]);
		$oAppointment->save();

		$this->sendConfirmationEmail(
			User::find($aAppointment['user_id'])->email,
			$aAppointment['name'],
			$request->input('date'),
			$request->input('time')
		);

		Flash()->success(__('Appointment has been rescheduled successfully. An email was sent to the user with appointment data.'))->important();

		return redirect('/admin/appointments');
	}

	public function validateStatus($piAppointmentId, $psStatus='granted') {
		$oAppointment = Appointment::find($piAppointmentId);

		return ($oAppointment->status == $psStatus) ? $oAppointment->toArray() : [];
	}

	public function sendConfirmationEmail($psTo, $psName, $psDate, $psTime) {
		$oDateTime = new Date("{$psDate} {$psTime}");

		$oContent = new \stdClass();

		$oContent->sName = $psName;
		$oContent->sDate = $oDateTime->format('j').' '.__('of').' '.$oDateTime->format('F');
		$oContent->sTime = $oDateTime->format('H:i a');

		// Send confirmation email
		Mail::to($psTo)->send(new AppointmentConfirmed($oContent));
	}
}
