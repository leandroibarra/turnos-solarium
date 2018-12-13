<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableExceptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exceptions', function (Blueprint $table) {
			// Primary Key
			$table->bigIncrements('id');

			// Columns
			$table->dateTime('datetime_from');
			$table->dateTime('datetime_to');
			$table->enum('type', ['holiday', 'other'])->default('holiday');
			$table->text('observations')->nullable();
			$table->tinyInteger('enable')->default(1);
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
		Schema::dropIfExists('exceptions');
    }
}
