<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBranchesWorkingWeek extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches_working_week', function (Blueprint $table) {
			// Primary Key
            $table->bigIncrements('id');

			// Columns
			$table->integer('branch_id')->unsigned();
			$table->tinyInteger('day_number')->unsigned();
			$table->tinyInteger('is_working_day')->unsigned()->default(0);
			$table->time('from')->nullable();
			$table->time('until')->nullable();
            $table->timestamps();

            // Unique Constraints
			$table->unique(['branch_id', 'day_number']);

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
        Schema::dropIfExists('branches_working_week');
    }
}
