<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'prices';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['branch_id', 'price', 'title', 'description', 'order'];

	/**
	 * The branch that price belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function branch()
	{
		return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
	}

	/**
	 * Retrieve enabled prices belonging to a branch.
	 *
	 * @param integer $piBranchId
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabled($piBranchId) {
		return $this
			->where('branch_id', '=', $piBranchId)
			->where('enable', '=', 1)
			->orderBy('order', 'ASC')
			->get();
	}

	/**
	 * Retrieve amount of enabled prices belonging to a branch.
	 *
	 * @param integer $piBranchId
	 * @return \Illuminate\Support\Collection
	 */
	public function getAmountEnabled($piBranchId) {
		return $this
			->where('branch_id', '=', $piBranchId)
			->where('enable', '=', 1)
			->count();
	}
}
