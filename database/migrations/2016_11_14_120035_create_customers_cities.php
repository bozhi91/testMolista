<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersCities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('customers_cities', function (Blueprint $table) {
			$table->primary(['customer_id', 'city_id']);
			$table->bigInteger('customer_id')->unsigned()->index();
			$table->bigInteger('city_id')->unsigned()->index();
			$table->foreign('customer_id')
					->references('id')
					->on('customers')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->foreign('city_id')
					->references('id')
					->on('districts')
					->onUpdate('cascade')
					->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('customers_cities');
	}

}
