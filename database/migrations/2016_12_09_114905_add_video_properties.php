<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVideoProperties extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('properties_videos', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('property_id')->unsigned()->index();
			$table->string('link');
			$table->string('thumbnail')->nullable();
			$table->integer('position_video')->nullable();
			$table->integer('position_media')->nullable();
			$table->timestamps();

			$table->foreign('property_id')
					->references('id')
					->on('properties')
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
		Schema::drop('properties_videos');
	}
}
