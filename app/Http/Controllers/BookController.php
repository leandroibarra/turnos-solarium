<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Appointment;

use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BookController extends Controller
{
	/**
	 * Show calendar to select appointment date.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function index(Request $request)
	{
		// Clean session data to prevent errors
		Session::forget('date');
		Session::forget('time');

		$oPrice = new Price();

		$oAppointment = new Appointment();

		return view('web.book')->with([
			'sDecimalPointSeparator' => config('app.decimal_point_separator'),
			'oBranch' => current($request->attributes)['oBranch'],
			'aEnabledPrices' => $oPrice->getEnabled(current($request->attributes)['oBranch']->id)->each(function($poPrice) {
				$poPrice->price = formatPrice($poPrice->price);
			}),
			'aGrantedAppointments' => $oAppointment->getTodayAndNextGrantedByUser(
				current($request->attributes)['oBranch']->id,
				Auth::user()->id
			)
		]);
	}

	/**
	 * Show appointment confirmation form.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create(Request $request)
	{
		return view('web.confirm')->with([
			'date' => Session::get('date'),
			'time' => Session::get('time'),
			'oDateTime' => new Date(Session::get('date').' '.Session::get('time')),
			'oBranch' => current($request->attributes)['oBranch']
		]);
	}
}
