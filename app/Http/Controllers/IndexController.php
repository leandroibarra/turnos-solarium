<?php

namespace App\Http\Controllers;

use App\Models\SiteParameter;

class IndexController extends Controller
{
	/**
	 * Show initial public page.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		return view('index')->with([
			'aSiteParameter' => SiteParameter::find(1)->toArray()
		]);
	}
}
