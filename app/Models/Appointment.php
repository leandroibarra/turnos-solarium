<?php

namespace App\Models;

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
	protected $fillable = ['branch_id', 'parent_appointment_id', 'user_id', 'date', 'time', 'name', 'phone', 'comment'];

	/**
	 * The user that appointment belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	/**
	 * The branch that appointment belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function branch()
	{
		return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
	}

	/**
	 * Retrieve granted appointments belonging to branch and date grouped by time.
	 *
	 * @param integer $piBranchId
	 * @param string $psDate
	 * @return \Illuminate\Support\Collection
	 */
	public function getGrantedByDate($piBranchId, $psDate) {
		return DB::table($this->table)
			->selectRaw("COUNT(*) AS amount, TIME_FORMAT(time, '%H:%i') AS time")
			->where('branch_id', '=', $piBranchId)
			->where('date', '=', $psDate)
			->where('status', '=', 'granted')
			->groupBy('time')
			->get();
	}

	/**
	 * Retrieve granted appointments belonging to branch between two date and times.
	 *
	 * @param integer $piBranchId
	 * @param string $psDateFrom
	 * @param string $psDateTo
	 * @return \Illuminate\Support\Collection
	 */
	public function getGrantedBetweenDates($piBranchId, $psDateFrom, $psDateTo) {
		return $this
			->where('branch_id', '=', $piBranchId)
			->where('date', '>=', $psDateFrom)
			->where('date', '<=', $psDateTo)
			->where('status', '=', 'granted')
			->get();
	}

	/**
	 * Retrieve granted appointments belonging to branch for today and the future.
	 *
	 * @param integer $piBranchId
	 * @return \Illuminate\Support\Collection
	 */
	public function getTodayAndNextGranted($piBranchId) {
		return $this
			->where('branch_id', '=', $piBranchId)
			->where('date', '>=', date('Y-m-d'))
			->where('status', '=', 'granted')
			->orderBy('date', 'ASC')
			->orderBy('time', 'ASC')
			->get();
	}
}
