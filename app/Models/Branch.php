<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'branches';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'address', 'city', 'province', 'country', 'amount_appointments_by_time'];

	/**
	 * The working week belonging to the branch.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function workingWeek()
	{
		return $this->hasMany('App\Models\Branch_Working_Week');
	}

	/**
	 * The appointments records belonging to the branch.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function appointments()
	{
		return $this->hasMany('App\Models\Appointment');
	}

	/**
	 * The exceptions records belonging to the branch.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function exceptions()
	{
		return $this->hasMany('App\Models\Exception');
	}

	/**
	 * Retrieve enabled branches.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabled()
	{
		return $this
			->where('enable', '=', 1)
			->orderBy('name', 'ASC')
			->get();
	}

	/**
	 * Retrieve amount of enabled branches.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getAmountEnabled()
	{
		return $this
			->where('enable', '=', 1)
			->count();
	}
}