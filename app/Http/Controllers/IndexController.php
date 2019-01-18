<?php

namespace App\Http\Controllers;

use App\Models\SiteParameter;
use App\Models\Price;
use App\Models\Slide;

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
		$oSlide = new Slide();

		return view('index')->with([
			'aSiteParameter' => SiteParameter::find(1)->toArray(),
			'sDecimalPointSeparator' => config('app.decimal_point_separator'),
			'sThousandsSeparator' => config('app.thousands_separator'),
			'aEnabledPrices' => $oPrice->getEnabled()->each(function($poPrice) {
				$poPrice->price = formatPrice($poPrice->price);
			}),
			'aEnabledSlides' => $oSlide->getEnabled()->each(function($poSlide) {
				$poSlide->fullPath = imageFullPath('slides', $poSlide->image);
			})
		]);
	}
}
