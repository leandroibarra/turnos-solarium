<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function edit($id) {
    	return view('admin.user-permission')->with([
    		'aUser' => User::find($id),
			'aModulesPermissions' => $this->getModulesAndPermissions()
		]);
	}

	public function update(Request $request, $id) {
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

		if (!(bool) $oUser->toArray()) {
			Flash()->error(__('Parametros no son validos'))->important();

			return redirect('admin/users');
		}

		$oUser->syncPermissions($request->input('permission'));

		Flash()->success(__('Los permisos se guardaron exitosamente'))->important();

		return redirect('admin/users');
	}

	public function getModulesAndPermissions() {
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
