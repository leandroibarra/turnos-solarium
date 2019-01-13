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
	protected $fillable = ['price', 'title', 'description', 'order'];

	/**
	 * Retrieve enabled prices.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getEnabled() {
		return $this
			->where('enable', '=', 1)
			->orderBy('order', 'ASC')
			->get();
	}

	/**
	 * Retrieve amount of enabled prices.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getAmountEnabled() {
		return $this
			->where('enable', '=', 1)
			->count();
	}
}
