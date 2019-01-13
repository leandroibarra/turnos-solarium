<?php
/**
 * Validates if available appointment in this date and time of week.
 *
 * @param string $psTime
 * @param array $paGrantedAppointments
 * @return boolean
 */
function validateGrantedAppointments($psTime, $paGrantedAppointments) {
	$iKey = array_search($psTime, array_column($paGrantedAppointments, 'time'));

	if ($iKey === false)
		return false;

	// Cast stdClass Object to array to prevent errors
	$paGrantedAppointments[$iKey] = (array) $paGrantedAppointments[$iKey];

	$oTime = new \Jenssegers\Date\Date($psTime);

	return !(bool) (
		($paGrantedAppointments[$iKey]['amount']<1 && $oTime->format('G')<14) ||
		($paGrantedAppointments[$iKey]['amount']<2 && $oTime->format('G')>=14)
	);
}

/**
 * Validates if working date is available to grant appointments.
 *
 * @param string $psDate
 * @param array $paExceptions
 * @return boolean $bInException
 */
function validateDateInExceptions($psDate, $paExceptions) {
	$bInException = false;

	$oDate = new \Jenssegers\Date\Date($psDate);

	$oDateTimeFrom = clone $oDate;
	$oDateTimeTo = clone $oDate;

	$oDateTimeFrom->hour(config('app.working_hours_per_day')[$oDate->format('w')][0]);

	$oDateTimeTo->hour(config('app.working_hours_per_day')[$oDate->format('w')][1]);

	foreach ($paExceptions as $aException)
		if (
			$bInException = (bool) (
				$aException['datetime_from'] <= $oDateTimeFrom->format('Y-m-d H:i:s') &&
				$aException['datetime_to'] >= $oDateTimeTo->format('Y-m-d H:i:s')
			)
		)
			break;

	return $bInException;
}

/**
 * Validates if working date and time is available to grant appointments.
 *
 * @param $psDateTime
 * @param $paExceptions
 * @return boolean $bInException
 */
function validateDateTimeInException($psDateTime, $paExceptions) {
	$bInException = false;

	$oDateTime = new \Jenssegers\Date\Date($psDateTime);

	foreach ($paExceptions as $aException) {
		$aException = (array) $aException;

		if (
			$bInException = (bool) (
				$aException['datetime_from'] <= $oDateTime->format('Y-m-d H:i:s') &&
				$aException['datetime_to'] >= $oDateTime->format('Y-m-d H:i:s')
			)
		)
			break;
	}

	return $bInException;
}

/**
 * Format price with configured decimal point separator and thousands separator.
 *
 * @param string $psPrice
 * @return string
 */
function formatPrice($psPrice)
{
	return number_format($psPrice, 2, config('app.decimal_point_separator'), config('app.thousands_separator'));
}