<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteParameter extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'site_parameters';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['about_tanning_text', 'pinterest_url', 'facebook_url', 'twitter_url', 'instagram_url'];
}
