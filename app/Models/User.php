<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
	use Notifiable, HasRoles;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	/**
	 * The appointments assigned to the user.
	 */
	public function appointments()
	{
		return $this->hasMany('App\Models\Appointment');
	}

	/**
	 * The branch that user belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function branch()
	{
		return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
	}

	/**
	 * Retrieve users with related data from last appointment.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getWithLastRelatedData() {
		return DB::table($this->table)
			->leftJoinSub(
				DB::table('appointments')
					->select('user_id', 'name', 'phone')
					->groupBy('user_id')
					->orderBy('created_at', 'DESC'),
				'latest_appointment',
				function($join) {
					$join->on("{$this->table}.id", '=', 'latest_appointment.user_id');
				}
			)->get();
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPasswordNotification($token));
	}
}
