<?php

use Illuminate\Database\Seeder;
use \App\Models\Branch;
use \App\Models\Branch_Working_Week;

class DefaultBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oBranch = Branch::create([
        	'name' => 'Buenos Aires',
			'address' => '9 de Julio 1200',
			'city' => 'Buenos Aires',
			'province' => 'Buenos Aires',
			'country' => 'Argentina',
			'country_code' => 'AR',
			'amount_appointments_by_time' => 2,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

        for ($iDay=0; $iDay<=6; $iDay++)
        	Branch_Working_Week::create([
        		'branch_id' => $oBranch->id,
				'day_number' => $iDay,
				'is_working_day' => (in_array($iDay, [0, 6])) ? 0 : 1,
				'from' => (in_array($iDay, [0, 6])) ? null : '09:00',
				'until' => (in_array($iDay, [0, 6])) ? null : '19:40',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
    }
}
