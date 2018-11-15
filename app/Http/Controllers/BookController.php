<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
	{

$sCurrentDate = date('Y-m-d H:i:s');
		$a = [
			'd' => $sCurrentDate,
			'w' => date('w'),
			// First day of the month.
			'f' => date('Y-m-01', strtotime($sCurrentDate)),
			// Last day of the month.
			'l' => date('Y-m-t', strtotime($sCurrentDate)),
		];

		return view('book');
	}

}
