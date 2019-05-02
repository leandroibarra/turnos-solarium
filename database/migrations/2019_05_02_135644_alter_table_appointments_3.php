<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAppointments3 extends Migration
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
			$table->integer('status_changed_by_user_id')->unsigned()->nullable()->after('status');

			// Foreign Key Constraints
			$table->foreign('status_changed_by_user_id')->references('id')->on('users')->onDelete('cascade');
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
			$table->dropForeign('appointments_status_changed_by_user_id_foreign');

			// Columns
			$table->dropColumn(['status_changed_by_user_id']);
        });
    }
}
