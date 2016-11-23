<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApiPublishLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('api_publications', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('site_id')->unsigned();
			$table->bigInteger('marketplace_id')->unsigned();
			$table->binary('property');
			$table->binary('result');
			$table->timestamps();
			
			$table->foreign('site_id')->references('id')
					->on('sites')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('marketplace_id')->references('id')
					->on('marketplaces')->onUpdate('cascade')->onDelete('cascade');
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('api_publications');
	}

}
