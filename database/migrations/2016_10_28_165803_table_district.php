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
		
		
		Schema::table('properties', function (Blueprint $table) {
			$table->bigInteger('district_id')->nullable()
					->unsigned()->index()->after('district');
			
			$table->foreign('district_id')
					->references('id')
					->on('districts')
					->onUpdate('set null')
					->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('properties', function (Blueprint $table) {
			$table->dropForeign('properties_district_id_foreign');
			$table->dropColumn('district_id');
		});
		
		Schema::drop('districts');
	}

}
