<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Exception extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'exceptions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['datetime_from', 'datetime_to', 'type', 'observations'];

	/**
	 * Retrieve current and future enabled exceptions.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getCurrentAndNextEnabled() {
		$sCurrentDateTime = date('Y-m-d H:i:s');

		return $this
			->whereRaw('(? BETWEEN datetime_from AND datetime_to OR datetime_from>?) AND enable=?', [$sCurrentDateTime, $sCurrentDateTime, 1])
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}

	/**
	 * Retrieve enabled exceptions between two date and times.
	 *
	 * @param string $psDateTimeFrom
	 * @param string $psDateTimeTo
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabledBetweenDates($psDateTimeFrom, $psDateTimeTo) {
		return $this
			->orWhereRaw('datetime_from BETWEEN ? AND ? AND enable=?', [$psDateTimeFrom, $psDateTimeTo, 1])
			->orWhereRaw('datetime_to BETWEEN ? AND ? AND enable=?', [$psDateTimeFrom, $psDateTimeTo, 1])
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}

	/**
	 * Retrieve enabled exceptions by date.
	 *
	 * @param string $psDate
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabledByDate($psDate) {
		return $this
			->whereRaw('? BETWEEN DATE_FORMAT(datetime_from, \'%Y-%m-%d\') AND DATE_FORMAT(datetime_to, \'%Y-%m-%d\')', [$psDate])
			->where('enable', '=', '1')
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}

	/**
	 * Retrieve enabled exceptions by date and time.
	 *
	 * @param string $psDateTime
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabledByDateAndTime($psDateTime) {
		return $this
			->whereRaw('? BETWEEN datetime_from AND datetime_to', [$psDateTime])
			->where('enable', '=', '1')
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}
}
