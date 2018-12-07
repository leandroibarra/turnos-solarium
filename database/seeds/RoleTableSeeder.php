<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Role::create([
			'name' => 'Sysadmin',
			'slug' => 'sysadmin',
			'special' => 'all-access'
		]);

        Role::create([
        	'name' => 'Admin',
			'slug' => 'admin'
		]);
    }
}
