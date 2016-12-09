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
		Schema::table('properties', function (Blueprint $table) {
			$table->string('video_link')->after('comment');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('properties', function (Blueprint $table) {
			$table->dropColumn('video_link');
		});
	}
}
