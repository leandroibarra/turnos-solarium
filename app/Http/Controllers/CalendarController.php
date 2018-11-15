<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class CalendarController extends Controller
{
    public function index($piYear, $piMonth, Request $request) {
		$oToday = new Date();

		$oLimitDate = clone $oToday;
		$oLimitDate->modify('+60 days');

		$oRequestDateTime = new Date("{$piYear}-{$piMonth}");

		$oLimitPrevNav = clone $oRequestDateTime;
		$oLimitPrevNav->modify('first day of this month')->modify('-1 day');

		$oLimitNextNav = clone $oRequestDateTime;
		$oLimitNextNav->modify('last day of this month')->modify('+1 day');

		$oDateTime = clone $oRequestDateTime;
		$oDateTime->modify('first day of this month')->modify('+1 day')->modify('last Sunday');

		$oHeaderDateTime = clone $oDateTime;

		$sOutput = "
			<header>\n
				<div class=\"row p-1\">\n
					<div class=\"col-2 text-center\">\n
		";

		if ($oLimitPrevNav->format('Y-m-d')<$oRequestDateTime->format('Y-m-d') && $oLimitPrevNav->format('m')+1!=$oToday->format('m'))
			$sOutput .= "
				<h2>\n
					<a data-year=\"{$oLimitPrevNav->format('Y')}\" data-month=\"{$oLimitPrevNav->format('m')}\" class=\"prev-month\">\n
						<i class=\"arrow left\"></i>\n
					</a>\n
				</h2>\n
			";

		$sOutput .= "
			</div>\n
			<div class=\"col-8\">\n
				<h2 class=\"text-center\">{$oRequestDateTime->format('F Y')}</h2>\n
			</div>\n
			<div class=\"col-2 text-center\">\n
		";

		if ($oLimitNextNav->format('Y-m-d')>$oToday->format('Y-m-d') && $oLimitNextNav->format('Y-m-d')<$oLimitDate->format('Y-m-d'))
			$sOutput .= "
				<h2>\n
					<a data-year=\"{$oLimitNextNav->format('Y')}\" data-month=\"{$oLimitNextNav->format('m')}\" class=\"next-month\">\n
						<i class=\"arrow right\"></i>\n
					</a>\n
				</h2>\n
			";

		$sOutput .= "
					</div>\n
				</div>\n
				<div class=\"row d-none d-sm-flex p-1 bg-dark text-white\">\n
		";

		for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++) {
			$sOutput .= "<h5 class=\"col-sm p-1 mb-0 text-center\">{$oHeaderDateTime->format('l')}</h5>\n";

			$oHeaderDateTime->modify('+1 day');
		}

		$sOutput .= "
				</div>\n
			</header>\n
			<div class=\"row border border-right-0 border-bottom-0\">\n
		";

		$iDays = 1;

		do {
			for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++) {
				if ($oToday>$oDateTime || $oDateTime->format('Y-m-d')>=$oLimitDate->format('Y-m-d') || $oDateTime->format('Y-m')!=$oRequestDateTime->format('Y-m'))
					$sClasses = 'd-sm-inline-block bg-light text-muted';
				else
					if (in_array($iWeekDay, config('app.non_working_days')))
						$sClasses = 'weekend-day text-muted';
					else
						$sClasses = 'bookable-day';

				$sClasses .= ($oToday == $oDateTime) ? ' current-day' : '';

				$sOutput .= "
					<div class=\"day col-sm p-2 border border-left-0 border-top-0 text-truncate {$sClasses}\"
						".((strpos($sClasses, 'bookable-day') !== false) ? "
						data-year=\"{$oDateTime->format('Y')}\"
						data-month=\"{$oDateTime->format('m')}\"
						data-day=\"{$oDateTime->format('j')}\"
						data-month-label=\"{$oDateTime->format('F')}\"
						data-target=\"#appointmentModal\"
						data-toggle=\"modal\"
						":"")."
					>\n
						<h5 class=\"row align-items-center\">\n
							<span class=\"date col-1\">{$oDateTime->format('j')}</span>\n
							<small class=\"col d-sm-none text-center text-muted\">{$oDateTime->format('l')}</small>\n
							<span class=\"col-1\"></span>\n
						</h5>\n
					</div>\n
				";

				if ($iWeekDay == 6)
					$sOutput .= "<div class=\"w-100\"></div>\n";

				$oDateTime->modify('+1 day');

				$iDays++;
			}
		} while ($iDays <= 42);

		$sOutput .= "</div>\n";

		return Response($sOutput);
	}
}
