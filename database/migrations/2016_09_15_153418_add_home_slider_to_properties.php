<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHomeSliderToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function (Blueprint $table) {
			$table->boolean('home_slider')->after('highlighted');
		});
	}
	public function down()
	{
		Schema::table('properties', function (Blueprint $table) {
			$table->dropColumn('home_slider');
		});
	}
}
