<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableDistrict extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('districts', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned()->index();
			$table->string('name');
			$table->timestamps();

			$table->foreign('site_id')
					->references('id')
					->on('sites')
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
		Schema::drop('districts');
	}

}
