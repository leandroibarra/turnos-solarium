<?php

use Illuminate\Database\Seeder;

class SiteParameterTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\App\Models\SiteParameter::create([
			'about_tanning_text' => null,
			'pinterest_url' => null,
			'facebook_url' => null,
			'twitter_url' => null,
			'instagram_url' => null,
			'created_at' => date('Y-m-d H:m:s'),
			'updated_at' => date('Y-m-d H:m:s')
		]);
	}
}
