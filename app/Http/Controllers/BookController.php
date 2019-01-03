<?php

namespace App\Http\Controllers;

use Jenssegers\Date\Date;

use Illuminate\Support\Facades\Session;

class BookController extends Controller
{
	/**
	 * Show calendar to select appointment date.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function index()
	{
		// Clean session data to prevent errors
		Session::forget('date');
		Session::forget('time');

		return view('web.book');
	}

	/**
	 * Show appointment confirmation form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		return view('web.confirm')->with([
			'date' => Session::get('date'),
			'time' => Session::get('time'),
			'oDateTime' => new Date(Session::get('date').' '.Session::get('time'))
		]);
	}
}
