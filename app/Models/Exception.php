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
	protected $fillable = ['branch_id', 'datetime_from', 'datetime_to', 'type', 'observations'];

	/**
	 * The branch that exception belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function branch()
	{
		return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
	}

	/**
	 * Retrieve current and future enabled exceptions belonging to a branch.
	 *
	 * @param integer $piBranchId
	 * @return \Illuminate\Support\Collection
	 */
	public function getCurrentAndNextEnabled($piBranchId) {
		$sCurrentDateTime = date('Y-m-d H:i:s');

		return $this
			->whereRaw('(? BETWEEN datetime_from AND datetime_to OR datetime_from>?) AND branch_id=? AND enable=?', [$sCurrentDateTime, $sCurrentDateTime, $piBranchId, 1])
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}

	/**
	 * Retrieve enabled exceptions between two date and times belonging to a branch.
	 *
	 * @param integer $piBranchId
	 * @param string $psDateTimeFrom
	 * @param string $psDateTimeTo
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabledBetweenDates($piBranchId, $psDateTimeFrom, $psDateTimeTo) {
		return $this
			->orWhereRaw('datetime_from BETWEEN ? AND ? AND branch_id=? AND enable=?', [$psDateTimeFrom, $psDateTimeTo, $piBranchId, 1])
			->orWhereRaw('datetime_to BETWEEN ? AND ? AND branch_id=? AND enable=?', [$psDateTimeFrom, $psDateTimeTo, $piBranchId, 1])
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}

	/**
	 * Retrieve enabled exceptions by date belonging to a branch.
	 *
	 * @param integer $piBranchId
	 * @param string $psDate
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabledByDate($piBranchId, $psDate) {
		return $this
			->whereRaw('? BETWEEN DATE_FORMAT(datetime_from, \'%Y-%m-%d\') AND DATE_FORMAT(datetime_to, \'%Y-%m-%d\')', [$psDate])
			->where('branch_id', '=', $piBranchId)
			->where('enable', '=', '1')
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}

	/**
	 * Retrieve enabled exceptions by date and time belonging to a branch.
	 *
	 * @param integer $piBranchId
	 * @param string $psDateTime
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabledByDateAndTime($piBranchId, $psDateTime) {
		return $this
			->whereRaw('? BETWEEN datetime_from AND datetime_to', [$psDateTime])
			->where('branch_id', '=', $piBranchId)
			->where('enable', '=', '1')
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}
}
