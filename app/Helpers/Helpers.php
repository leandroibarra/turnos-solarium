<?php
/**
 * Validates if available appointment in this date and time of week.
 *
 * @param string $psTime
 * @param array $paGrantedAppointments
 * @param integer $piAppointmentsByTime
 * @return boolean
 */
function validateGrantedAppointments($psTime, $paGrantedAppointments, $piAppointmentsByTime) {
	$iKey = array_search($psTime, array_column($paGrantedAppointments, 'time'));

	if ($iKey === false)
		return false;

	// Cast stdClass Object to array to prevent errors
	$paGrantedAppointments[$iKey] = (array) $paGrantedAppointments[$iKey];

	return !(bool) ($paGrantedAppointments[$iKey]['amount'] < $piAppointmentsByTime);
}

/**
 * Validates if working date is available to grant appointments.
 *
 * @param array $aWorkingHoursPerDay
 * @param string $psDate
 * @param array $paExceptions
 * @return boolean $bInException
 */
function validateDateInExceptions($aWorkingHoursPerDay, $psDate, $paExceptions) {
	$bInException = false;

	$oDate = new \Jenssegers\Date\Date($psDate);

	$oDateTimeFrom = clone $oDate;
	$oDateTimeTo = clone $oDate;

	// Set hours, minutes, and seconds to date time form and date time to
	foreach ([$oDateTimeFrom, $oDateTimeTo] as $iKey=>$oDateTime) {
		list($iHour, $iMinute, $iSecond) = explode(':', $aWorkingHoursPerDay[$oDate->format('w')][$iKey]);

		$oDateTime->hour($iHour);
		$oDateTime->minute($iMinute);
		$oDateTime->second($iSecond);
	}

	// Find if there are any exception between day working hours
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

/**
 * Build image full path.
 *
 * @param string $psResource
 * @param string $psImageName
 * @return string
 */
function imageFullPath($psResource, $psImageName) {
	return implode('/', [
		config('filesystems.disks')[config('filesystems.default')]['url'],
		config('app.path_folder_by_resource')[$psResource],
		$psImageName
	]);
}