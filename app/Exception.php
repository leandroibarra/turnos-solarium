<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
	 * Retrieve enabled exceptions for the future.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getNextEnabled() {
		return $this
			->where('datetime_from', '>', date('Y-m-d H:i:s'))
			->where('enable', '=', '1')
			->orderBy('datetime_from', 'ASC')
			->orderBy('datetime_to', 'ASC')
			->get();
	}
}
