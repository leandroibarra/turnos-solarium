<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAppointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
			// Primary Key
			$table->bigIncrements('id');

			// Columns
			$table->integer('user_id')->unsigned();
			$table->date('date');
			$table->time('time');
			$table->string('name');
			$table->string('phone');
			$table->text('comment')->nullable();
			$table->enum('status', ['granted', 'cancelled', 'rescheduled'])->default('granted');
            $table->timestamps();

			// Foreign Key Constraints
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
