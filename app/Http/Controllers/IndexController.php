<?php

namespace App\Http\Controllers;

use App\Models\SiteParameter;
use App\Models\Price;

class IndexController extends Controller
{
	/**
	 * Show initial public page.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$oPrice = new Price();

		return view('index')->with([
			'aSiteParameter' => SiteParameter::find(1)->toArray(),
			'aEnabledPrices' => $oPrice->getEnabled()->toArray()
		]);
	}
}
