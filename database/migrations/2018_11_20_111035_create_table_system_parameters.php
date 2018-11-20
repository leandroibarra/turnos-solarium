<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSystemParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_parameters', function (Blueprint $table) {
			// Primary Key
			$table->increments('id');

			// Columns
			$table->enum('appointment_minutes', [10, 12, 15, 20, 30])->default(10);
			$table->enum('appointment_until_days', [15, 30, 60, 90, 120])->default(60);
			$table->string('appointment_confirmed_email_subject');
			$table->text('appointment_confirmed_email_body');
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
		Schema::dropIfExists('system_parameters');
    }
}
