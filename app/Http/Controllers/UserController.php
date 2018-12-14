<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list() {
    	$oUser = new User();
    	$aUsers = $oUser->getWithLastRelatedData();

    	return view('admin.user')->with([
    		'aUsers' => User::hydrate($aUsers->toArray())
		]);
	}
}
