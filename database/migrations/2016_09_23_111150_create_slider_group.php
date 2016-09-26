<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSliderGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('slider_group', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->bigInteger('site_id')->unsigned()->index();
			$table->boolean('isAllLocales');
			$table->timestamps();
		});

		Schema::create('slider_group_locale', function(Blueprint $table) {
			$table->primary(['group_id', 'locale_id']);
			$table->integer('group_id')->unsigned()->index();
			$table->integer('locale_id')->unsigned()->index();
		});

		Schema::create('slider_image', function (Blueprint $table) {
			$table->increments('id');
			$table->string('image');
			$table->string('link');
			$table->integer('position');
			$table->integer('group_id')->unsigned()->index();
			$table->timestamps();
		});
		
		//FKs
		Schema::table('slider_group', function($table) {
			$table->foreign('site_id')
					->references('id')
					->on('sites')
					->onUpdate('cascade')
					->onDelete('cascade');
		});
		
		Schema::table('slider_group_locale', function($table) {
			$table->foreign('group_id')
					->references('id')
					->on('slider_group')
					->onUpdate('cascade')
					->onDelete('cascade');

			$table->foreign('locale_id')
					->references('id')
					->on('locales')
					->onUpdate('cascade')
					->onDelete('cascade');
		});

		Schema::table('slider_image', function($table) {
			$table->foreign('group_id')
					->references('id')
					->on('slider_group')
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
		Schema::drop('slider_group_locale');
		Schema::drop('slider_image');
		Schema::drop('slider_group');
	}

}
