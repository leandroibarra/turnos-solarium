<?php

namespace App\Http\Controllers;

use App\Models\Exception;
use App\Models\SystemParameter;

use Jenssegers\Date\Date;

class CalendarController extends Controller
{
	/**
	 * List available days in an specific year and month.
	 *
	 * @param integer $piYear
	 * @param integer $piMonth
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function index($piYear, $piMonth)
	{
    	$aSystemParameters = SystemParameter::find(1)->toArray();

		$oToday = new Date();

		// Build deadline for appointments booking
		$oLimitDate = clone $oToday;
		$oLimitDate->modify("+{$aSystemParameters['appointment_until_days']} days");

		$oRequestDate = new Date("{$piYear}-{$piMonth}");

		// Set previous navigation limit
		$oLimitPrevNav = clone $oRequestDate;
		$oLimitPrevNav->modify('first day of this month')->modify('-1 day');

		// Set next navigation limit
		$oLimitNextNav = clone $oRequestDate;
		$oLimitNextNav->modify('last day of this month')->modify('+1 day');

		// Set first available day in calendar
		$oDate = clone $oRequestDate;
		$oDate->modify('first day of this month')->modify('+1 day')->modify('last Sunday');

		$oHeaderDateTime = clone $oDate;

		$oException = new  Exception();

		return view('web.partials.calendar')->with([
			'oToday' => $oToday,
			'oLimitDate' => $oLimitDate,
			'oRequestDate' => $oRequestDate,
			'oLimitPrevNav' => $oLimitPrevNav,
			'oLimitNextNav' => $oLimitNextNav,
			'oDate' => $oDate,
			'oHeaderDateTime' => $oHeaderDateTime,
			'aNonWorkingDays' => config('app.non_working_days'),
			'aExceptions' => $oException->getEnabledBetweenDates(
				$oToday->format('Y-m-d H:i:s'),
				$oLimitDate->format('Y-m-d H:i:s')
			)->toArray()
		]);
	}
}
