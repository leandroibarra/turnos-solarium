<?php

namespace App\Http\Controllers;

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

		return view('web.book')->with([
			'oBranch' => current($request->attributes)['oBranch']
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
