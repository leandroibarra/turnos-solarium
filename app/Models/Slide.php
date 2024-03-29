<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'slides';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'image', 'link', 'order'];

	/**
	 * Retrieve enabled slides.
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
	 * Retrieve amount of enabled slides.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getAmountEnabled() {
		return $this
			->where('enable', '=', 1)
			->count();
	}
}
