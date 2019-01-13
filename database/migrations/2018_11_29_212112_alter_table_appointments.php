<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAppointments extends Migration
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
			$table->bigInteger('parent_appointment_id')->unsigned()->nullable()->after('id');

			// Foreign Key Constraints
			$table->foreign('parent_appointment_id')->references('id')->on('appointments')->onDelete('cascade');
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
			$table->dropForeign('appointments_parent_appointment_id_foreign');

			// Columns
			$table->dropColumn(['parent_appointment_id']);
        });
    }
}
