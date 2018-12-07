<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Appointment extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'appointments';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['parent_appointment_id', 'user_id', 'date', 'time', 'name', 'phone', 'comment'];

	/**
	 * The user that appointment belongs to.
	 */
	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}

	/**
	 * Retrieve granted appointments belonging to date grouped by time.
	 *
	 * @param string $psDate
	 * @return \Illuminate\Support\Collection
	 */
	public function getGrantedByDate($psDate) {
		return DB::table('appointments')
			->select(DB::raw("COUNT(*) AS amount, TIME_FORMAT(time, '%H:%i') AS time"))
			->where('date', '=', $psDate)
			->where('status', '=', 'granted')
			->groupBy('time')
			->get();
	}

	/**
	 * Retrieve granted appointments between two date and times.
	 *
	 * @param string $psDateFrom
	 * @param string $psDateTo
	 * @return \Illuminate\Support\Collection
	 */
	public function getGrantedBetweenDates($psDateFrom, $psDateTo) {
		return $this
			->where('date', '>=', $psDateFrom)
			->where('date', '<=', $psDateTo)
			->where('status', '=', 'granted')
			->get();
	}

	/**
	 * Retrieve granted appointments for the future.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getNextGranted() {
		return $this
			->where('date', '>', date('Y-m-d H:i:s'))
			->where('status', '=', 'granted')
			->orderBy('date', 'ASC')
			->orderBy('time', 'ASC')
			->get();
	}
}
