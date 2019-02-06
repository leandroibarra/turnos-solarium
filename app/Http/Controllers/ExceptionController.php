<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Exception;
use App\Models\SystemParameter;

use Jenssegers\Date\Date;

use Illuminate\Http\Request;

class ExceptionController extends Controller
{
	/**
	 * @var array
	 */
	private $aSystemParameters;

	/**
	 * @var string
	 */
	private $sPregMatchFormat;

	/**
	 * @var string
	 */
	private $sFromFormat;

	/**
	 * ExceptionController constructor.
	 */
	public function __construct()
	{
		$this->aSystemParameters = SystemParameter::find(1)->toArray();

		$this->sPregMatchFormat = str_replace(
			['/','YYYY', 'MM', 'DD', 'HH', 'mm'],
			['\/', '\d{4}', '\d{2}', '\d{2}', '\d{2}', '\d{2}'],
			__('YYYY-MM-DD HH:mm')
		);

		$this->sFromFormat = str_replace(
			['YYYY', 'MM', 'DD', 'HH', 'mm'],
			['Y', 'm', 'd', 'H', 'i'],
			__('YYYY-MM-DD HH:mm')
		);
	}

	/**
	 * List next enabled exceptions.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list()
	{
		$oException = new Exception();

		return view('admin.exception')->with([
			'aEnabledExceptions' => $oException->getCurrentAndNextEnabled()
		]);
	}

	/**
	 * Delete logically (disable) an enabled exception.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete(Request $request, $id)
	{
		if ($request->ajax()) {
			$aException = Exception::find($id);

			if ((bool) $aException->enable) {
				Exception::whereId($id)->update([
					'enable' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				]);

				$aResponse = [
					'status' => 'success',
					'message' => __('The exception has been deleted successfully.')
				];
			} else {
				$aResponse = [
					'status' => 'error',
					'message' => __('The exception already has been deleted. Please, update your page.')
				];
			}

			return response()->json($aResponse);
		}
	}

	/**
	 * Show exception creation form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		return view('admin.exception-create')->with([
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes']
		]);
	}

	/**
	 * Store a new exception.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function store(Request $request)
	{
		// Validate request
		$this->validateException($request);

		// Create and save exception
		$oException = new Exception($request->all());
		$oException->save();

		Flash()->success(__('The exception has been created successfully.'))->important();

		return redirect('/admin/exceptions');
	}

	/**
	 * Show exception edition form.
	 *
	 * @param integer $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function edit($id)
	{
		$oException = Exception::find($id);

		// Validate exception status
		if (!(bool) $oException || !(bool) $oException->enable) {
			Flash()->error(__('The exception is not valid or has been deleted.'))->important();

			return redirect('/admin/exceptions');
		}

		// Format date and time range field data
		$oException->datetimes = implode(__(' - '), [
			date($this->sFromFormat, strtotime($oException->datetime_from)),
			date($this->sFromFormat, strtotime($oException->datetime_to))
		]);

		return view('admin.exception-edit')->with([
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes'],
			'aException' => $oException->toArray()
		]);
	}

	/**
	 * Update the give exception.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		$oException = Exception::find($id);

		// Validate exception status
		if (!(bool) $oException || !(bool) $oException->enable) {
			Flash()->error(__('The exception is not valid or has been deleted.'))->important();

			return redirect('/admin/exceptions');
		}

		// Validate request
		$this->validateException($request);

		// Update exception
		Exception::whereId($id)->update([
			'datetime_from' => $request->input('datetime_from'),
			'datetime_to' => $request->input('datetime_to'),
			'type' => $request->input('type'),
			'observations' => $request->input('observations'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Flash()->success(__('The exception has been edited successfully.'))->important();

		return redirect('/admin/exceptions');
	}

	/**
	 * Validate request.
	 *
	 * @param Request $poRequest
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validateException(&$poRequest)
	{
		$this->validate(
			$poRequest,
			[
				'datetimes' => [
					'required',
					function ($attribute, $value, $fail) use ($poRequest) {
						// Validate date and time range format
						if (preg_match('/^'.$this->sPregMatchFormat.__(' - ').$this->sPregMatchFormat.'$/', $value) != 1) {
							$fail(__('The :attribute field is not valid.'));
						} else {
							list($sDateTimeFrom, $sDateTimeTo) = explode(__(' - '), $value);

							$sDateTimeFrom = Date::createFromFormat($this->sFromFormat, $sDateTimeFrom);
							$sDateTimeTo = Date::createFromFormat($this->sFromFormat, $sDateTimeTo);

							// Validate date and time from less than date and time to
							if ($sDateTimeFrom >= $sDateTimeTo) {
								$fail(__('The :attribute field is not valid.'));
							} else {
								$oAppointment = new Appointment();
								$iAppointment = count($oAppointment->getGrantedBetweenDates($sDateTimeFrom, $sDateTimeTo)->toArray());

								// Validate fi there are any appointment into date and time range
								if ((bool) $iAppointment) {
									$fail(trans_choice('plurals.datetimes', $iAppointment));
								} else {
									// Complete rest of data
									$poRequest->request->set('datetime_from', $sDateTimeFrom->format('Y-m-d H:i'));
									$poRequest->request->set('datetime_to', $sDateTimeTo->format('Y-m-d H:i'));
								}
							}
						}
					}
				],
				'type' => [
					'required',
					function ($attribute, $value, $fail) {
						if (!in_array($value, ['holiday', 'other']))
							$fail(__('The :attribute field is not valid.'));
					}
				]
			],
			[],
			[
				'datetimes' => strtolower(__('Date and time range')),
				'type' => strtolower(__('Type'))
			]
		);
	}
}
