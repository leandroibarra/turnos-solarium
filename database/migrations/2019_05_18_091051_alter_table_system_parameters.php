<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSystemParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('system_parameters', function (Blueprint $table) {
			// Columns
			$table->string('appointment_cancelled_email_subject')->after('appointment_confirmed_email_body');
			$table->text('appointment_cancelled_email_body')->after('appointment_cancelled_email_subject');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('system_parameters', function (Blueprint $table) {
			// Columns
			$table->dropColumn(['appointment_cancelled_email_subject', 'appointment_cancelled_email_body']);
		});
    }
}
