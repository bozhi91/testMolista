<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesRatesTable extends Migration
{
	public function up()
	{
		Schema::create('currencies_rates', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->char('from', 3)->index();
			$table->char('to', 3)->index();
			$table->double('rate',10,3);
			$table->timestamps();

			$table->unique([ 'from', 'to' ]);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('currencies_rates');
	}
}
