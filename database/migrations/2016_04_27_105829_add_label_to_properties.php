<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLabelToProperties extends Migration
{
	public function up()
	{
		Schema::table('properties', function(Blueprint $table)
		{
			$table->string('label_color')->after('currency');
		});
		Schema::table('properties_translations', function(Blueprint $table)
		{
			$table->string('label')->after('description');
		});
	}

	public function down()
	{
		Schema::table('properties_translations', function(Blueprint $table)
		{
			$table->dropColumn('label');
		});
		Schema::table('properties', function(Blueprint $table)
		{
			$table->dropColumn('label_color');
		});
	}
}
