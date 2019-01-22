<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Exception;
use App\Models\SystemParameter;
use App\Models\User;
use App\Mails\AppointmentConfirmed;

use Jenssegers\Date\Date;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
	/**
	 * List available appointments in an specific date.
	 *
	 * @param integer $piYear
	 * @param integer $piMonth
	 * @param integer $piDay
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index($piYear, $piMonth, $piDay, Request $request)
	{
		// Build request date
		$sRequestDate = "{$piYear}-{$piMonth}-{$piDay}";

		$oRequestDate = new Date($sRequestDate);

		// Obtain working hours per day from app configs
		$aWorkingHoursPerDay = config('app.working_hours_per_day')[$oRequestDate->format('w')];

		$aHours = range($aWorkingHoursPerDay[0], $aWorkingHoursPerDay[1], 1);

		$aMorning = $aAfternoon = $aNight = [];

		// Divide each working hor into morning, afternoon, and night
		foreach ($aHours as $iHour)
			if ($iHour < 12)
				$aMorning[] = $iHour;
			else if ($iHour>=12 && $iHour<17)
				$aAfternoon[] = $iHour;
			else
				$aNight[] = $iHour;

		$aSystemParameters = SystemParameter::find(1)->toArray();

		$oAppointment = new Appointment();

		$oException = new Exception();

		return view('web.partials.appointment')->with([
			'iAppointmentMinutes' => $aSystemParameters['appointment_minutes'],
			'iAppointmentsPerHour' => 60 / $aSystemParameters['appointment_minutes'],
			'oToday' => new Date(),
			'oRequestDate' => $oRequestDate,
			'aAppointmentToExclude' => ((bool) $request->headers->get('appointment-id')) ? $oAppointment::find($request->headers->get('appointment-id'))->toArray() : [],
			'aGrantedAppointments' => $oAppointment->getGrantedByDate($sRequestDate)->toArray(),
			'aMorning' => $aMorning,
			'aAfternoon' => $aAfternoon,
			'aNight' => $aNight,
			'aExceptions' => $oException->getEnabledByDate($oRequestDate->format('Y-m-d'))->toArray()
		]);
	}

	/**
	 * Put date and time request variables into date and time session variables.
	 *
	 * @param Request $request
	 */
	public function set(Request $request)
	{
		Session::put('date', $request->input('date'));
		Session::put('time', $request->input('time'));
	}

	/**
	 * Store a new appointment from user logged request.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function store(Request $request)
	{
		// Validate request
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

		$oException = new Exception();

		// Validate if there are any exception in appointment date and time
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

		// Create and save appointment
		$oAppointment = new Appointment($request->all());
		$oAppointment->save();

		// Send confirmation email to the user
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

	/**
	 * List next granted appointments.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list()
	{
		$oAppointment = new Appointment();

		return view('admin.appointment')->with([
			'aGrantedAppointments' => $oAppointment->getTodayAndNextGranted()
		]);
	}

	/**
	 * Cancel a granted appointment.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function cancel(Request $request, $id)
	{
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

	/**
	 * Reschedule a granted appointment.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(Request $request, $id)
	{
		// Validate if appointment has still granted status
		$aAppointment = $this->validateStatus($id, 'granted');

		if (!(bool) $aAppointment) {
			Flash()->error(__('This appointment already has been cancelled or rescheduled.'))->important();

			return redirect('/admin/appointments');
		}

		$oException = new Exception();

		// Validate if there are any exception in new appointment date and time
		if ((bool) $oException->getEnabledByDate($request->input('date').' '.$request->input('time'))->toArray()) {
			Flash()->error(__('Appointment could not been granted. Please, try again.'))->important();

			return redirect('/admin/appointments');
		}

		// Validate if there are any granted appointment in new appointment date and time
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

		// Update granted appointment with rescheduled status
		Appointment::whereId($aAppointment['id'])->update([
			'status' => 'rescheduled',
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Create and save new appointment
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

		// Send confirmation email to the user
		$this->sendConfirmationEmail(
			User::find($aAppointment['user_id'])->email,
			$aAppointment['name'],
			$request->input('date'),
			$request->input('time')
		);

		Flash()->success(__('Appointment has been rescheduled successfully. An email was sent to the user with appointment data.'))->important();

		return redirect('/admin/appointments');
	}

	/**
	 * Validate and present form to reschedule an appointment.
	 *
	 * @param integer $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function reschedule($id)
	{
		// Validate if appointment has still granted status
		$aAppointment = $this->validateStatus($id, 'granted');

		if (!(bool) $aAppointment) {
			Flash()->error(__('This appointment already has been cancelled or rescheduled.'))->important();

			return redirect('/admin/appointments');
		}

		return view('admin.appointment-reschedule')->with([
			'aAppointment' => $aAppointment
		]);
	}

	/**
	 * Validate if appointment has given status.
	 *
	 * @param integer $piAppointmentId
	 * @param string $psStatus OPTIONAL
	 * @return array
	 */
	public function validateStatus($piAppointmentId, $psStatus='granted')
	{
		$oAppointment = Appointment::find($piAppointmentId);

		return ($oAppointment->status == $psStatus) ? $oAppointment->toArray() : [];
	}

	/**
	 * Send confirmation email to the user with the given information.
	 *
	 * @param string $psTo
	 * @param string $psName
	 * @param string $psDate
	 * @param string $psTime
	 */
	public function sendConfirmationEmail($psTo, $psName, $psDate, $psTime)
	{
		$oDateTime = new Date("{$psDate} {$psTime}");

		$oContent = new \stdClass();

		$oContent->sName = $psName;
		$oContent->sDate = $oDateTime->format('j').' '.__('of').' '.$oDateTime->format('F');
		$oContent->sTime = $oDateTime->format('H:i a');

		// Send confirmation email
		Mail::to($psTo)->send(new AppointmentConfirmed($oContent));
	}
}
