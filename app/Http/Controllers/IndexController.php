<?php

namespace App\Http\Controllers;

use App\Models\SiteParameter;
use App\Models\Price;
use App\Models\Slide;
use App\Models\Branch;

class IndexController extends Controller
{
	/**
	 * Show initial public page.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$oSlide = new Slide();

		$aSystemParameter = SiteParameter::find(1)->toArray();
		$aSystemParameter['about_tanning_text'] = html_entity_decode($aSystemParameter['about_tanning_text']);

		$aBranchesPrices = [];

		foreach (Branch::with('enabledPrices')->get() as $oBranch) {
			if (!$oBranch->enabledPrices->isEmpty()) {
				$oBranch->enabledPrices->each(function($poPrice) {
					$poPrice->price = formatPrice($poPrice->price);
				});

				$aBranchesPrices[] = $oBranch->toArray();
			}
		}

		return view('index')->with([
			'aSiteParameter' => $aSystemParameter,
			'sDecimalPointSeparator' => config('app.decimal_point_separator'),
			'sThousandsSeparator' => config('app.thousands_separator'),
			'aEnabledSlides' => $oSlide->getEnabled()->each(function($poSlide) {
				$poSlide->fullPath = imageFullPath('slides', $poSlide->image);
			}),
			'aBranchesPrices' => $aBranchesPrices
		]);
	}
}
