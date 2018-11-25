<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class BookController extends Controller
{
    public function index() {
		// Clean session data to prevent errors
		Session::forget('date');
		Session::forget('time');

		return view('web.book');
	}

	public function create() {
		return view('web.confirm')->with([
			'date' => Session::get('date'),
			'time' => Session::get('time'),
			'oDateTime' => new Date(Session::get('date').' '.Session::get('time'))
		]);
	}
}
