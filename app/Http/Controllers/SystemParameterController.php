<?php

namespace App\Http\Controllers;

use App\Models\SystemParameter;

use Illuminate\Http\Request;

class SystemParameterController extends Controller
{
	/**
	 * Show system parameters edition form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function edit()
	{
    	return view('admin.system-parameters')->with([
    		'aSystemParameter' => SystemParameter::find(1)->toArray()
		]);
	}

	/**
	 * Update system parameters.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		// Validate request
		$this->validate(
			$request,
			[
				'appointment_until_days' => [
					'required',
					'in:15,30,60,90,120'
				],
				'appointment_confirmed_email_subject' => 'required',
				'appointment_confirmed_email_body' => 'required',
				'appointment_cancelled_email_subject' => 'required',
				'appointment_cancelled_email_body' => 'required'
			],
			[],
			[
				'appointment_until_days' => strtolower(__('Reservation days limit')),
				'appointment_confirmed_email_subject' => strtolower(__('Confirmation email subject')),
				'appointment_confirmed_email_body' => strtolower(__('Confirmation email body'))
			]
		);

		// Update system parameters
		SystemParameter::whereId($id)->update([
			'appointment_until_days' => $request->input('appointment_until_days'),
			'appointment_confirmed_email_subject' => strip_tags($request->input('appointment_confirmed_email_subject')),
			'appointment_confirmed_email_body' => htmlentities($request->input('appointment_confirmed_email_body')),
			'appointment_cancelled_email_subject' => strip_tags($request->input('appointment_cancelled_email_subject')),
			'appointment_cancelled_email_body' => htmlentities($request->input('appointment_cancelled_email_body'))
		]);

		Flash()->success(__('Parameters were saved successfully'))->important();

		return redirect('admin/system-parameters');
	}
}
