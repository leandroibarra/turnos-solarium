<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch_Working_Week extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'branches_working_week';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['branch_id', 'day_number', 'is_working_day', 'is_working_day', 'until'];

	/**
	 * The branch that working week (day) belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function branch()
	{
		return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
	}
}