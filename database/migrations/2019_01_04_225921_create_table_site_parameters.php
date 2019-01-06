<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSiteParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('site_parameters', function (Blueprint $table) {
			// Primary Key
			$table->increments('id');

			// Columns
			$table->text('about_tanning_text')->nullable();
			$table->string('pinterest_url')->nullable();
			$table->string('facebook_url')->nullable();
			$table->string('twitter_url')->nullable();
			$table->string('instagram_url')->nullable();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('site_parameters');
    }
}
