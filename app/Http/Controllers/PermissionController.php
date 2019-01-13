<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
	/**
	 * Show user permission edition form.
	 *
	 * @param integer $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function edit($id)
	{
    	return view('admin.user-permission')->with([
    		'aUser' => User::find($id),
			'aModulesPermissions' => $this->getModulesAndPermissions()
		]);
	}

	/**
	 * Update permissions to the given user.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		// Validate request
		$this->validate(
			$request,
			[
				'permission' => 'required'
			],
    		[],
    		[
    			'permission' => strtolower(__('Permissions'))
			]
		);

		$oUser = User::find($id);

		// Validate user ID
		if (!(bool) $oUser->toArray()) {
			Flash()->error(__('Parameters are not valid'))->important();

			return redirect('admin/users');
		}

		// Remove all current user permissions and set the given ones
		$oUser->syncPermissions($request->input('permission'));

		Flash()->success(__('Permissions were saved successfully'))->important();

		return redirect('admin/users');
	}

	/**
	 * Retrieve permissions grouped by modules.
	 *
	 * @return array $aModulesPermissions
	 */
	public function getModulesAndPermissions()
	{
    	$aModulesPermissions = [];

		foreach (Permission::all()->toArray() as $aPermission) {
			$sKey = explode('.', $aPermission['name'])[1];

			if (in_array($sKey, ['user', 'permission']))
				$aModulesPermissions['user'][] = $aPermission['name'];
			else
				$aModulesPermissions[$sKey][] = $aPermission['name'];
		}

		return $aModulesPermissions;
	}
}
