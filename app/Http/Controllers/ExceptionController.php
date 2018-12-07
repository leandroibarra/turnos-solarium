<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Exception;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class ExceptionController extends Controller
{
	private $aSystemParameters;

	private $sPregMatchFormat;

	private $sFromFormat;

	public function __construct()
	{
		$this->aSystemParameters = \App\SystemParameter::find(1)->toArray();

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

	public function list() {
		$oException = new Exception();

		return view('admin.exception')->with([
			'aEnabledExceptions' => $oException->getNextEnabled()
		]);
	}

	public function delete(Request $request, $id) {
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
					'message' => __('The exception could not be deleted. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}

	public function create() {
		return view('admin.exception-create')->with([
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes']
		]);
	}

	public function store(Request $request) {
		$this->validateException($request);

		$oException = new Exception($request->all());
		$oException->save();

		Flash()->success(__('The exception has been created successfully.'))->important();

		return redirect('/admin/exceptions');
	}

	public function edit($id) {
		$oException = Exception::find($id);

		if (!(bool) $oException || !(bool) $oException->enable) {
			Flash()->error(__('The exception is not valid or has been deleted.'))->important();

			return redirect('/admin/exceptions');
		}

		$oException->datetimes = implode(__(' - '), [
			date($this->sFromFormat, strtotime($oException->datetime_from)),
			date($this->sFromFormat, strtotime($oException->datetime_to))
		]);

		return view('admin.exception-edit')->with([
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes'],
			'aException' => $oException->toArray()
		]);
	}

	public function update(Request $request, $id) {
		$oException = Exception::find($id);

		if (!(bool) $oException || !(bool) $oException->enable) {
			Flash()->error(__('The exception is not valid or has been deleted.'))->important();

			return redirect('/admin/exceptions');
		}

		$this->validateException($request);

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

	public function validateException(&$poRequest) {
		$this->validate(
			$poRequest,
			[
				'datetimes' => [
					'required',
					function ($attribute, $value, $fail) use ($poRequest) {


						if (preg_match('/^'.$this->sPregMatchFormat.__(' - ').$this->sPregMatchFormat.'$/', $value) != 1) {
							$fail(__('The :attribute field is not valid.'));
						} else {
							list($sDateTimeFrom, $sDateTimeTo) = explode(__(' - '), $value);

							$sDateTimeFrom = Date::createFromFormat($this->sFromFormat, $sDateTimeFrom);
							$sDateTimeTo = Date::createFromFormat($this->sFromFormat, $sDateTimeTo);

							if ($sDateTimeFrom >= $sDateTimeTo) {
								$fail(__('The :attribute field is not valid.'));
							} else {
								$oAppointment = new Appointment();
								$iAppointment = count($oAppointment->getGrantedBetweenDates($sDateTimeFrom, $sDateTimeTo)->toArray());

								if ((bool) $iAppointment) {
									$fail(trans_choice('plurals.datetimes', $iAppointment));
								} else {
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
