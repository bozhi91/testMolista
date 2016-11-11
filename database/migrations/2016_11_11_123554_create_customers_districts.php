<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersDistricts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('customers_districts', function (Blueprint $table) {
			$table->primary(['customer_id', 'district_id']);
			$table->bigInteger('customer_id')->unsigned()->index();
			$table->bigInteger('district_id')->unsigned()->index();

			$table->foreign('customer_id')
					->references('id')
					->on('customers')
					->onUpdate('cascade')
					->onDelete('cascade');

			$table->foreign('district_id')
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
		Schema::drop('customers_districts');
	}

}
