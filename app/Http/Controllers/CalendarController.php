<?php

namespace App\Http\Controllers;

use Jenssegers\Date\Date;

class CalendarController extends Controller
{
    public function index($piYear, $piMonth) {
    	$aSystemParameters = \App\SystemParameter::find(1)->toArray();

		$oToday = new Date();

		$oLimitDate = clone $oToday;
		$oLimitDate->modify("+{$aSystemParameters['appointment_until_days']} days");

		$oRequestDateTime = new Date("{$piYear}-{$piMonth}");

		$oLimitPrevNav = clone $oRequestDateTime;
		$oLimitPrevNav->modify('first day of this month')->modify('-1 day');

		$oLimitNextNav = clone $oRequestDateTime;
		$oLimitNextNav->modify('last day of this month')->modify('+1 day');

		$oDateTime = clone $oRequestDateTime;
		$oDateTime->modify('first day of this month')->modify('+1 day')->modify('last Sunday');

		$oHeaderDateTime = clone $oDateTime;

		$oException = new  \App\Exception();

		return view('web.partials.calendar')->with([
			'oToday' => $oToday,
			'oLimitDate' => $oLimitDate,
			'oRequestDateTime' => $oRequestDateTime,
			'oLimitPrevNav' => $oLimitPrevNav,
			'oLimitNextNav' => $oLimitNextNav,
			'oDateTime' => $oDateTime,
			'oHeaderDateTime' => $oHeaderDateTime,
			'aNonWorkingDays' => config('app.non_working_days'),
			'aExceptions' => $oException->getEnabledBetweenDates(
				$oToday->format('Y-m-d H:i:s'),
				$oLimitDate->format('Y-m-d H:i:s')
			)->toArray()
		]);
	}
}
