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
        Permission::create([
			'name' => 'admin.system-parameters.edit',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.system-parameters.update',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.appointment.list',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.appointment.cancel',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.appointment.reschedule',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.appointment.update',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.exception.list',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.exception.edit',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.exception.update',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.exception.delete',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.exception.create',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		Permission::create([
			'name' => 'admin.exception.store',
			'guard_name' => 'web',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);
    }
}
