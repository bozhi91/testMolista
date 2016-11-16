<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPropertyIdToApiPublications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('api_publications', function (Blueprint $table) {
			$table->bigInteger('property_id')->unsigned()->nullable()->after('marketplace_id');

			$table->foreign('property_id')->references('id')
					->on('properties')->onUpdate('cascade')
					->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('api_publications', function (Blueprint $table) {
			$table->dropColumn('property_id');
		});
	}

}
