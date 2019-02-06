<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
			// Primary Key
			$table->increments('id');

			// Columns
			$table->decimal('price', 7, 2)->unsigned();
			$table->string('title');
			$table->text('description');
			$table->tinyInteger('order')->unsigned()->nullable();
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
        Schema::dropIfExists('prices');
    }
}
