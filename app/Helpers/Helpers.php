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