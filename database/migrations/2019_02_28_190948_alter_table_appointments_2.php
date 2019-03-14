<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAppointments2 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('appointments', function (Blueprint $table) {
			// Columns
			$table->integer('branch_id')->unsigned()->nullable()->after('id');

			// Foreign Key Constraints
			$table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('appointments', function (Blueprint $table) {
			// Foreign Key Constraints
			$table->dropForeign('appointments_branch_id_foreign');

			// Columns
			$table->dropColumn(['branch_id']);
		});
	}
}
