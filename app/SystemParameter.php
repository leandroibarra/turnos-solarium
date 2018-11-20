<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemParameter extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'system_parameters';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['appointment_minutes', 'appointment_until_days', 'appointment_confirmed_email_subject', 'appointment_confirmed_email_body'];
}
