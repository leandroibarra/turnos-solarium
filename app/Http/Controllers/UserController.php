<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
	/**
	 * List users with last related data.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function list()
	{
    	$oUser = new User();

    	return view('admin.user')->with([
    		'aUsers' => User::hydrate($oUser->getWithLastRelatedData()->toArray())
		]);
	}
}
