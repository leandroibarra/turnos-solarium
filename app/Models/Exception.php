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
			->whereRaw('? BETWEEN datetime_from AND datetime_to AND enable=?', [$sCurrentDateTime, 1])
			->orWhere('datetime_from', '>', $sCurrentDateTime)
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
			->orWhereRaw('? BETWEEN datetime_from AND datetime_to AND enable=?', [$psDateTimeFrom, 1])
			->orWhereRaw('? BETWEEN datetime_from AND datetime_to AND enable=?', [$psDateTimeTo, 1])
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
	public function getEnabledByDate($psDateTime) {
		return DB::table($this->table)
			->whereRaw('? BETWEEN datetime_from AND datetime_to', [$psDateTime])
			->where('enable', '=', '1')
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}
}
