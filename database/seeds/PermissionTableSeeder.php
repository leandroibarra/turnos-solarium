<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$aPermissions = [
			'admin.system-parameters.edit',
			'admin.system-parameters.update',
			'admin.appointment.list',
			'admin.appointment.cancel',
			'admin.appointment.reschedule',
			'admin.appointment.update',
			'admin.exception.list',
			'admin.exception.edit',
			'admin.exception.update',
			'admin.exception.delete',
			'admin.exception.create',
			'admin.exception.store',
			'admin.user.list',
			'admin.permission.edit',
			'admin.permission.update',
			'admin.site-parameters.edit',
			'admin.site-parameters.update'
		];

    	foreach ($aPermissions as $sPermission)
			Permission::create([
				'name' => $sPermission,
				'guard_name' => 'web',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
    }
}
